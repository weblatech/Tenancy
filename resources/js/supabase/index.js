/**
 * Supabase WhatsApp CRM — Module Barrel Export
 *
 * Import everything from this file for convenient access:
 *   import { supabase, getContacts, getChatHistory, subscribeToNewMessages } from './supabase/index.js';
 */

export { supabase } from './config.js';
export { fetchRows, fetchSingle, insertRows, updateRows, deleteRows, callFunction } from './db.js';
export { getContacts, getContactByPhone } from './contacts.js';
export { getChatHistory, getChatByPhone, sendMessage, getLastMessage, getMessageCount } from './chat.js';
export { subscribeToNewMessages, subscribeToMessageUpdates, subscribeToAllChanges, unsubscribe, unsubscribeAll, getActiveSubscriptionCount } from './realtime.js';
