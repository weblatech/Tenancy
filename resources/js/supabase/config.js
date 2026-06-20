import { createClient } from '@supabase/supabase-js';

/**
 * Supabase Client Configuration
 *
 * Initializes and exports a singleton Supabase client instance
 * used across the entire WhatsApp CRM application.
 *
 * Tables expected in Supabase:
 *   - orders:          id, customer, mobile, status, price
 *   - whatsapp_chats:  id, order_id, sender_phone, message_body, direction, created_at
 */

const SUPABASE_URL = 'https://zwdumolledeoxlvqckka.supabase.co';
const SUPABASE_ANON_KEY = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';

export const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
    realtime: {
        params: {
            eventsPerSecond: 10,
        },
    },
    db: {
        schema: 'public',
    },
});

export default supabase;
