import { supabase } from './config.js';

/**
 * Real-time Subscriptions Service Module
 *
 * Manages Supabase Realtime channel subscriptions for the
 * 'whatsapp_chats' table. Enables instant UI updates when
 * new messages are inserted — no page refresh required.
 *
 * Architecture:
 *   - Each subscription is tracked by a named channel
 *   - Filters allow subscribing to specific order_ids
 *   - Cleanup functions prevent memory leaks and duplicate listeners
 */

// Active subscription channels keyed by identifier
const activeChannels = {};

/**
 * Subscribe to real-time INSERT events on whatsapp_chats.
 *
 * When a new row is inserted into whatsapp_chats, the callback
 * fires immediately with the new message payload.
 *
 * @param {string}   identifier      - Unique name for this subscription (e.g., 'order-42')
 * @param {Function} onInsert        - Callback receiving the new message row: { id, order_id, sender_phone, message_body, direction, created_at }
 * @param {object}   options         - Optional configuration
 * @param {number}   options.orderId - Filter to only receive events for a specific order_id
 * @returns {object} Channel reference for manual unsubscribe
 */
export function subscribeToNewMessages(identifier, onInsert, options = {}) {
    // Clean up existing subscription with the same identifier
    unsubscribe(identifier);

    const filter = options.orderId
        ? `order_id=eq.${options.orderId}`
        : undefined;

    const channel = supabase
        .channel(`realtime:whatsapp_chats:${identifier}`)
        .on(
            'postgres_changes',
            {
                event: 'INSERT',
                schema: 'public',
                table: 'whatsapp_chats',
                ...(filter ? { filter } : {}),
            },
            (payload) => {
                if (onInsert && typeof onInsert === 'function') {
                    onInsert(payload.new);
                }
            }
        )
        .subscribe((status) => {
            if (status === 'SUBSCRIBED') {
                console.log(`[Realtime] Subscribed to whatsapp_chats (${identifier})`);
            } else if (status === 'CHANNEL_ERROR') {
                console.error(`[Realtime] Channel error for ${identifier}`);
            }
        });

    activeChannels[identifier] = channel;
    return channel;
}

/**
 * Subscribe to real-time UPDATE events on whatsapp_chats.
 *
 * Useful for tracking message edits or status changes in the future.
 *
 * @param {string}   identifier    - Unique name for this subscription
 * @param {Function} onUpdate      - Callback receiving the updated row
 * @param {object}   options       - Optional filter configuration
 * @returns {object} Channel reference
 */
export function subscribeToMessageUpdates(identifier, onUpdate, options = {}) {
    unsubscribe(identifier);

    const filter = options.orderId
        ? `order_id=eq.${options.orderId}`
        : undefined;

    const channel = supabase
        .channel(`realtime:whatsapp_chats_updates:${identifier}`)
        .on(
            'postgres_changes',
            {
                event: 'UPDATE',
                schema: 'public',
                table: 'whatsapp_chats',
                ...(filter ? { filter } : {}),
            },
            (payload) => {
                if (onUpdate && typeof onUpdate === 'function') {
                    onUpdate(payload.new);
                }
            }
        )
        .subscribe();

    activeChannels[identifier] = channel;
    return channel;
}

/**
 * Subscribe to ALL changes (INSERT, UPDATE, DELETE) on whatsapp_chats.
 *
 * @param {string}   identifier - Unique name for this subscription
 * @param {Function} onChange   - Callback receiving { eventType, new, old }
 * @param {object}   options    - Optional filter configuration
 * @returns {object} Channel reference
 */
export function subscribeToAllChanges(identifier, onChange, options = {}) {
    unsubscribe(identifier);

    const filter = options.orderId
        ? `order_id=eq.${options.orderId}`
        : undefined;

    const channel = supabase
        .channel(`realtime:whatsapp_chats_all:${identifier}`)
        .on(
            'postgres_changes',
            {
                event: '*',
                schema: 'public',
                table: 'whatsapp_chats',
                ...(filter ? { filter } : {}),
            },
            (payload) => {
                if (onChange && typeof onChange === 'function') {
                    onChange({
                        eventType: payload.eventType,
                        new: payload.new,
                        old: payload.old,
                    });
                }
            }
        )
        .subscribe();

    activeChannels[identifier] = channel;
    return channel;
}

/**
 * Unsubscribe from a specific named channel.
 *
 * @param {string} identifier - The subscription identifier to remove
 */
export function unsubscribe(identifier) {
    if (activeChannels[identifier]) {
        supabase.removeChannel(activeChannels[identifier]);
        delete activeChannels[identifier];
        console.log(`[Realtime] Unsubscribed: ${identifier}`);
    }
}

/**
 * Unsubscribe from ALL active channels.
 *
 * Call this on component teardown or page navigation to
 * prevent memory leaks and orphaned WebSocket connections.
 */
export function unsubscribeAll() {
    for (const identifier of Object.keys(activeChannels)) {
        supabase.removeChannel(activeChannels[identifier]);
        delete activeChannels[identifier];
    }
    console.log('[Realtime] All subscriptions removed');
}

/**
 * Get the count of currently active subscriptions.
 *
 * @returns {number}
 */
export function getActiveSubscriptionCount() {
    return Object.keys(activeChannels).length;
}

export default {
    subscribeToNewMessages,
    subscribeToMessageUpdates,
    subscribeToAllChanges,
    unsubscribe,
    unsubscribeAll,
    getActiveSubscriptionCount,
};
