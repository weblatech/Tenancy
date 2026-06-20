import { supabase } from './config.js';

/**
 * Contacts Service Module
 *
 * Handles all contact-related operations.
 * Contacts are derived from the 'orders' table, grouped by unique
 * phone numbers so each customer appears once regardless of order count.
 *
 * Data structure returned per contact:
 * {
 *   id:            number   - Latest order ID
 *   customer:      string   - Customer display name
 *   mobile:        string   - Phone number (unique key for grouping)
 *   status:        string   - Current order status / label
 *   price:         number   - Order price
 *   total_orders:  number   - Count of orders for this phone
 *   last_message:  string|null - Most recent chat message body
 *   last_message_at: string|null - Timestamp of most recent message
 * }
 */

/**
 * Fetch all contacts grouped by unique phone number.
 *
 * Strategy:
 *   1. Fetch all orders ordered by id DESC (latest first)
 *   2. Group by mobile phone in JS — first occurrence = latest order
 *   3. For each unique phone, attach the last message from whatsapp_chats
 *
 * @returns {Promise<{data: Array|null, error: object|null}>}
 */
export async function getContacts() {
    // Step 1: Fetch all orders (latest first)
    const { data: orders, error: ordersError } = await supabase
        .from('orders')
        .select('id, customer, mobile, status, price')
        .order('id', { ascending: false });

    if (ordersError) {
        return { data: null, error: ordersError };
    }

    // Step 2: Group by unique mobile — keep first (latest) occurrence per phone
    const seen = new Set();
    const uniqueContacts = [];

    for (const order of orders) {
        const phone = order.mobile?.trim();
        if (phone && !seen.has(phone)) {
            seen.add(phone);
            uniqueContacts.push({
                id: order.id,
                customer: order.customer || 'Unknown',
                mobile: phone,
                status: order.status || 'Unfulfilled',
                price: order.price || 0,
                total_orders: 0,
                last_message: null,
                last_message_at: null,
            });
        }
    }

    // Step 3: Count total orders per phone
    const phoneCounts = {};
    for (const order of orders) {
        const phone = order.mobile?.trim();
        if (phone) {
            phoneCounts[phone] = (phoneCounts[phone] || 0) + 1;
        }
    }
    for (const contact of uniqueContacts) {
        contact.total_orders = phoneCounts[contact.mobile] || 1;
    }

    // Step 4: Fetch last message for each unique phone
    if (uniqueContacts.length > 0) {
        const phones = uniqueContacts.map(c => c.mobile);

        const { data: lastMessages, error: msgError } = await supabase
            .from('whatsapp_chats')
            .select('sender_phone, message_body, created_at')
            .in('sender_phone', phones)
            .order('created_at', { ascending: false });

        if (!msgError && lastMessages) {
            const lastMsgMap = {};
            for (const msg of lastMessages) {
                // First occurrence is the latest due to ordering
                if (!lastMsgMap[msg.sender_phone]) {
                    lastMsgMap[msg.sender_phone] = {
                        message: msg.message_body,
                        at: msg.created_at,
                    };
                }
            }
            for (const contact of uniqueContacts) {
                const lm = lastMsgMap[contact.mobile];
                if (lm) {
                    contact.last_message = lm.message;
                    contact.last_message_at = lm.at;
                }
            }
        }
    }

    return { data: uniqueContacts, error: null };
}

/**
 * Fetch a single contact by their phone number.
 *
 * @param {string} phone - The phone number to look up
 * @returns {Promise<{data: object|null, error: object|null}>}
 */
export async function getContactByPhone(phone) {
    const { data: orders, error } = await supabase
        .from('orders')
        .select('id, customer, mobile, status, price')
        .eq('mobile', phone)
        .order('id', { ascending: false })
        .limit(1)
        .single();

    if (error) {
        return { data: null, error };
    }

    // Fetch last message
    const { data: lastMsg } = await supabase
        .from('whatsapp_chats')
        .select('message_body, created_at')
        .eq('sender_phone', phone)
        .order('created_at', { ascending: false })
        .limit(1)
        .single();

    return {
        data: {
            ...orders,
            last_message: lastMsg?.message_body || null,
            last_message_at: lastMsg?.created_at || null,
        },
        error: null,
    };
}

export default {
    getContacts,
    getContactByPhone,
};
