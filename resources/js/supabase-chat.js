/**
 * Supabase Chat — Vite Entry Point
 *
 * This file is the bridge between the Vite-compiled bundle and
 * the Blade template. It exposes all Supabase services to the
 * global `window.WACrm` namespace so inline scripts in Blade
 * can access them without import syntax.
 */

import {
    supabase,
    fetchRows,
    fetchSingle,
    insertRows,
    updateRows,
    deleteRows,
    getContacts,
    getContactByPhone,
    getChatHistory,
    getChatByPhone,
    sendMessage,
    getLastMessage,
    getMessageCount,
    subscribeToNewMessages,
    subscribeToMessageUpdates,
    subscribeToAllChanges,
    unsubscribe,
    unsubscribeAll,
} from './supabase/index.js';

/**
 * Expose all services under window.WACrm for Blade template access.
 *
 * Usage in Blade:
 *   const { data, error } = await window.WACrm.getContacts();
 *   const sub = window.WACrm.subscribeToNewMessages('order-42', callback, { orderId: 42 });
 */
window.WACrm = {
    // Client instance
    supabase,

    // DB operations
    fetchRows,
    fetchSingle,
    insertRows,
    updateRows,
    deleteRows,

    // Contacts
    getContacts,
    getContactByPhone,

    // Chat
    getChatHistory,
    getChatByPhone,
    sendMessage,
    getLastMessage,
    getMessageCount,

    // Realtime
    subscribeToNewMessages,
    subscribeToMessageUpdates,
    subscribeToAllChanges,
    unsubscribe,
    unsubscribeAll,
};

console.log('[WACrm] Supabase services initialized');
