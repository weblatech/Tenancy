import { supabase } from './config.js';

/**
 * Chat Service Module
 *
 * Handles all chat message operations for the WhatsApp CRM.
 * Messages are stored in the 'whatsapp_chats' table with fields:
 *   - id:           UUID (auto-generated)
 *   - order_id:     number (references orders.id)
 *   - sender_phone: string (customer phone number)
 *   - message_body: string (the message text)
 *   - direction:    'inbound' | 'outbound'
 *   - created_at:   ISO timestamp
 */

/**
 * Fetch the complete chat history for a specific order.
 *
 * Joins whatsapp_chats with orders to provide both the message
 * stream and the associated order context. Messages are returned
 * in chronological order (oldest first) for rendering top-to-bottom.
 *
 * @param {number} orderId - The order ID to fetch messages for
 * @returns {Promise<{data: {messages: Array, order: object|null}, error: object|null}>}
 */
export async function getChatHistory(orderId) {
    // Fetch messages for this order
    const { data: messages, error: msgError } = await supabase
        .from('whatsapp_chats')
        .select('*')
        .eq('order_id', orderId)
        .order('created_at', { ascending: true });

    if (msgError) {
        return { data: { messages: [], order: null }, error: msgError };
    }

    // Fetch associated order for context
    const { data: order, error: orderError } = await supabase
        .from('orders')
        .select('id, customer, mobile, status, price')
        .eq('id', orderId)
        .single();

    // Ignore order fetch error — messages are the priority
    return {
        data: {
            messages: messages || [],
            order: order || null,
        },
        error: null,
    };
}

/**
 * Fetch chat history for a specific customer by their phone number.
 *
 * Useful when multiple orders exist under one phone — this aggregates
 * all messages across all their orders.
 *
 * @param {string} phone - Customer phone number
 * @returns {Promise<{data: Array, error: object|null}>}
 */
export async function getChatByPhone(phone) {
    const { data, error } = await supabase
        .from('whatsapp_chats')
        .select('*')
        .eq('sender_phone', phone)
        .order('created_at', { ascending: true });

    return { data: data || [], error };
}

/**
 * Insert a new message into the whatsapp_chats table.
 *
 * @param {object}  message             - Message object
 * @param {number}  message.order_id    - Associated order ID
 * @param {string}  message.sender_phone - Customer phone number
 * @param {string}  message.message_body - Message text content
 * @param {string}  message.direction    - 'inbound' or 'outbound'
 * @returns {Promise<{data: object|null, error: object|null}>}
 */
export async function sendMessage(message) {
    const { data, error } = await supabase
        .from('whatsapp_chats')
        .insert({
            order_id: message.order_id,
            sender_phone: message.sender_phone,
            message_body: message.message_body,
            direction: message.direction,
            created_at: new Date().toISOString(),
        })
        .select()
        .single();

    return { data, error };
}

/**
 * Fetch the most recent message for a specific order.
 *
 * Used to update the contact list preview without refetching
 * the entire chat history.
 *
 * @param {number} orderId - The order ID
 * @returns {Promise<{data: object|null, error: object|null}>}
 */
export async function getLastMessage(orderId) {
    const { data, error } = await supabase
        .from('whatsapp_chats')
        .select('message_body, direction, created_at')
        .eq('order_id', orderId)
        .order('created_at', { ascending: false })
        .limit(1)
        .single();

    return { data, error };
}

/**
 * Count total messages for a specific order.
 *
 * @param {number} orderId - The order ID
 * @returns {Promise<{data: number, error: object|null}>}
 */
export async function getMessageCount(orderId) {
    const { count, error } = await supabase
        .from('whatsapp_chats')
        .select('*', { count: 'exact', head: true })
        .eq('order_id', orderId);

    return { data: count || 0, error };
}

export default {
    getChatHistory,
    getChatByPhone,
    sendMessage,
    getLastMessage,
    getMessageCount,
};
