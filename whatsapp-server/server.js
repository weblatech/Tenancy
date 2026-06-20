const express = require('express');
const cors = require('cors');
const QRCode = require('qrcode');
const { makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion, makeCacheableSignalKeyStore } = require('@whiskeysockets/baileys');
const { Boom } = require('@hapi/boom');
const { createClient } = require('@supabase/supabase-js');
const fs = require('fs');
const path = require('path');
const http = require('http');

const app = express();
app.use(cors());
app.use(express.json({ limit: '10mb' }));

const PORT = process.env.WA_SERVER_PORT || 3001;
const AUTH_DIR = process.env.WA_AUTH_DIR || path.join(__dirname, 'auth');
const WEBHOOK_URL = process.env.WA_WEBHOOK_URL || '';

// Supabase config
const SUPABASE_URL = 'https://zwdumolledeoxlvqckka.supabase.co';
const SUPABASE_KEY = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';
const supabase = createClient(SUPABASE_URL, SUPABASE_KEY);

// Active sessions: { [tenantId]: { sock, status, qr, phone, retries, channel } }
const sessions = {};

// LID-to-phone mapping cache: { [tenantId]: { lid: phone } }
const lidMap = {};

// Ensure auth directory exists
if (!fs.existsSync(AUTH_DIR)) fs.mkdirSync(AUTH_DIR, { recursive: true });

// ============================================================
// Session Management
// ============================================================

async function startSession(tenantId) {
    if (sessions[tenantId]?.sock) {
        try {
            sessions[tenantId].sock.end(undefined);
        } catch (e) {}
    }

    const authPath = path.join(AUTH_DIR, tenantId);
    if (!fs.existsSync(authPath)) fs.mkdirSync(authPath, { recursive: true });

    const { state, saveCreds } = await useMultiFileAuthState(authPath);
    const { version, isLatest } = await fetchLatestBaileysVersion();

    sessions[tenantId] = {
        sock: null,
        status: 'connecting',
        qr: null,
        phone: null,
        retries: 0,
    };

    const sock = makeWASocket({
        version,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys),
        },
        printQRInTerminal: false,
        logger: require('pino')({ level: 'silent' }),
        browser: ['Chrome (Linux)', '', ''],
        generateHighQualityLinkPreview: false,
        syncFullHistory: true,
        markOnlineOnConnect: true,
        patchMessageBeforeSending: (msg, ctx) => msg,
        appStateMacVersions: undefined,
    });

    sessions[tenantId].sock = sock;

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            try {
                sessions[tenantId].qr = await QRCode.toDataURL(qr, {
                    width: 300,
                    margin: 2,
                    color: { dark: '#075e54', light: '#ffffff' },
                });
            } catch (e) {
                sessions[tenantId].qr = null;
            }
            sessions[tenantId].status = 'qr';
        }

        if (connection === 'open') {
            sessions[tenantId].status = 'connected';
            sessions[tenantId].qr = null;
            sessions[tenantId].retries = 0;

            try {
                const user = sock.user;
                if (user?.id) {
                    let phone = user.id.replace(/[^0-9]/g, '');
                    // Fix Baileys bug: sometimes appends extra digits
                    // Pakistani numbers are 12 digits (92 + 10), trim if longer
                    if (phone.length > 12 && phone.startsWith('92')) {
                        console.log(`[${tenantId}] Baileys returned ${phone.length}-digit phone "${phone}", trimming to 12: "${phone.substring(0, 12)}"`);
                        phone = phone.substring(0, 12);
                    }
                    sessions[tenantId].phone = phone;
                }
            } catch (e) {}

            notifyStatus(tenantId, 'connected');

            // DON'T subscribe to Supabase realtime for outbound
            // Outbound is handled directly via /api/send from the chat panel
            // Supabase realtime is unreliable (CLOSED errors)
        }

        if (connection === 'close') {
            const reason = lastDisconnect?.error?.output?.statusCode;
            const isLoggedOut = reason === DisconnectReason.loggedOut;

            if (isLoggedOut) {
                sessions[tenantId].status = 'disconnected';
                sessions[tenantId].qr = null;
                sessions[tenantId].phone = null;
                sessions[tenantId].retries = 0;
                unsubscribeFromOutboundMessages(tenantId);
                notifyStatus(tenantId, 'disconnected');
                // Clean up auth files
                try {
                    fs.rmSync(authPath, { recursive: true, force: true });
                } catch (e) {}
            } else {
                sessions[tenantId].status = 'reconnecting';
                sessions[tenantId].retries++;
                unsubscribeFromOutboundMessages(tenantId);
                notifyStatus(tenantId, 'reconnecting');
                // Auto reconnect after delay
                const delay = Math.min(5000 * sessions[tenantId].retries, 30000);
                setTimeout(() => startSession(tenantId), delay);
            }
        }
    });

    // Track outbound messages for LID-to-phone mapping
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        for (const msg of messages) {
            // Track OUTBOUND messages to build LID mapping
            if (msg.key?.fromMe) {
                const jid = msg.key?.remoteJid || '';
                const phone = jid.replace(/[^0-9]/g, '');
                if (phone && !jid.endsWith('@lid') && !jid.endsWith('@g.us')) {
                    // Store mapping: this phone is associated with our WhatsApp
                    if (!lidMap[tenantId]) lidMap[tenantId] = {};
                    // We don't know the LID yet, but store for reference
                    sessions[tenantId] = sessions[tenantId] || {};
                    sessions[tenantId].lastOutboundPhone = phone;
                    sessions[tenantId].lastOutboundTime = Date.now();
                    console.log(`[${tenantId}] Outbound tracked: phone=${phone}`);
                }
                continue;
            }

            if (type !== 'notify') continue;

            // Skip protocol/system messages (no text, no media)
            if (!msg.message) continue;
            if (msg.message.protocolMessage) continue;
            if (msg.message.ephemeralMessage) continue;
            if (msg.message.pollCreationMessage) continue;
            if (msg.message.pollUpdateMessage) continue;
            if (msg.message.receiptMultiple) continue;

            const text = msg.message?.conversation
                || msg.message?.extendedTextMessage?.text
                || '';
            const remoteJid = msg.key?.remoteJid || '';
            const pushName = msg.pushName || '';
            const participant = msg.key?.participant || '';
            const timestamp = msg.messageTimestamp
                ? new Date(typeof msg.messageTimestamp === 'number' ? msg.messageTimestamp * 1000 : msg.messageTimestamp).toISOString()
                : new Date().toISOString();

            // Skip if no text AND no recognized media type
            const hasText = !!text;
            const hasMedia = !!(msg.message?.imageMessage || msg.message?.videoMessage || msg.message?.audioMessage || msg.message?.documentMessage || msg.message?.stickerMessage || msg.message?.locationMessage || msg.message?.contactMessage || msg.message?.reactionMessage || msg.message?.buttonsResponseMessage || msg.message?.listResponseMessage);
            if (!hasText && !hasMedia) continue;

            // Detect message type
            let messageType = 'text';
            let messageBody = text;
            if (msg.message?.imageMessage) { messageType = 'image'; messageBody = text || '[Image]'; }
            else if (msg.message?.videoMessage) { messageType = 'video'; messageBody = text || '[Video]'; }
            else if (msg.message?.audioMessage) { messageType = 'audio'; messageBody = text || '[Voice Message]'; }
            else if (msg.message?.documentMessage) { messageType = 'document'; messageBody = text || `[Document: ${msg.message.documentMessage.fileName || 'file'}]`; }
            else if (msg.message?.stickerMessage) { messageType = 'sticker'; messageBody = text || '[Sticker]'; }
            else if (msg.message?.locationMessage) { messageType = 'location'; messageBody = text || '[Location]'; }
            else if (msg.message?.contactMessage) { messageType = 'contact'; messageBody = text || '[Contact]'; }
            else if (msg.message?.reactionMessage) { messageType = 'reaction'; messageBody = msg.message.reactionMessage.text || '[Reaction]'; }
            else if (msg.message?.buttonsResponseMessage) { messageType = 'button'; messageBody = msg.message.buttonsResponseMessage.selectedButtonId || '[Button Response]'; }
            else if (msg.message?.listResponseMessage) { messageType = 'list'; messageBody = msg.message.listResponseMessage.singleSelectReply?.selectedRowId || '[List Response]'; }

            const isLid = remoteJid.endsWith('@lid');
            const isGroup = remoteJid.endsWith('@g.us');
            let from = remoteJid.replace(/[^0-9]/g, '') || '';

            if (isGroup && participant) {
                from = participant.replace(/[^0-9]/g, '');
            }

            if (!from) continue;

            console.log(`[${tenantId}] INCOMING: jid=${remoteJid} lid=${isLid} from=${from} pushName="${pushName}" msg="${text.substring(0, 50)}"`);

            // Resolve phone from LID
            let resolvedPhone = from;
            let orderId = null;

            if (isLid || from.length > 13) {
                console.log(`[${tenantId}] LID detected: ${from}`);

                // METHOD 1: Check lidMap cache (previously resolved LIDs)
                if (lidMap[tenantId]?.[from]) {
                    resolvedPhone = lidMap[tenantId][from];
                    console.log(`[${tenantId}] LID resolved from cache: ${from} → ${resolvedPhone}`);
                }

                // METHOD 2: pushName match with order customer names
                if (resolvedPhone === from && pushName) {
                    try {
                        const { data: orders } = await supabase
                            .from('orders')
                            .select('id,mobile,customer')
                            .order('id', { ascending: false })
                            .limit(50);

                        if (orders) {
                            const name = pushName.toLowerCase().trim();
                            for (const order of orders) {
                                if (order.customer) {
                                    const custName = order.customer.toLowerCase().trim();
                                    if (name === custName || name.includes(custName) || custName.includes(name)) {
                                        resolvedPhone = (order.mobile || '').replace(/[^0-9]/g, '');
                                        console.log(`[${tenantId}] LID resolved via pushName "${pushName}" → order #${order.id} (${resolvedPhone})`);
                                        break;
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.log(`[${tenantId}] pushName match error:`, e.message);
                    }
                }

                // METHOD 3: Match LID to most recently messaged order
                if (resolvedPhone === from) {
                    try {
                        const { data: lastOutbound } = await supabase
                            .from('whatsapp_chats')
                            .select('order_id')
                            .eq('direction', 'outbound')
                            .not('order_id', 'is', null)
                            .order('created_at', { ascending: false })
                            .limit(1)
                            .single();

                        if (lastOutbound?.order_id) {
                            const { data: order } = await supabase
                                .from('orders')
                                .select('mobile')
                                .eq('id', lastOutbound.order_id)
                                .single();

                            if (order?.mobile) {
                                resolvedPhone = order.mobile.replace(/[^0-9]/g, '');
                                orderId = lastOutbound.order_id;
                                console.log(`[${tenantId}] LID resolved via recent outbound: ${from} → order #${orderId} (${resolvedPhone})`);
                            }
                        }
                    } catch (e) {
                        console.log(`[${tenantId}] Recent outbound match error:`, e.message);
                    }
                }
            }

            // Cache the mapping for future
            if (resolvedPhone !== from && (isLid || from.length > 13)) {
                if (!lidMap[tenantId]) lidMap[tenantId] = {};
                lidMap[tenantId][from] = resolvedPhone;
                console.log(`[${tenantId}] LID mapping cached: ${from} → ${resolvedPhone}`);
            } else if (resolvedPhone === from && (isLid || from.length > 13)) {
                console.log(`[${tenantId}] Could not resolve LID ${from} — pushName: "${pushName}"`);
            }

            // Match to order by resolved phone
            if (orderId === null) {
                try {
                    const { data: orders } = await supabase
                        .from('orders')
                        .select('id,mobile')
                        .order('id', { ascending: false })
                        .limit(100);

                    if (orders) {
                        const normalizedFrom = resolvedPhone.replace(/[^0-9]/g, '');
                        const fromLocal = normalizedFrom.startsWith('92') ? '0' + normalizedFrom.substring(2) : normalizedFrom;
                        const fromIntl = normalizedFrom.startsWith('92') ? normalizedFrom : '92' + normalizedFrom.replace(/^0/, '');

                        for (const order of orders) {
                            const dbPhone = (order.mobile || '').replace(/[^0-9]/g, '');
                            const dbLocal = dbPhone.startsWith('92') ? '0' + dbPhone.substring(2) : dbPhone;
                            const dbIntl = dbPhone.startsWith('92') ? dbPhone : '92' + dbPhone.replace(/^0/, '');

                            if (normalizedFrom === dbPhone || normalizedFrom === dbLocal || normalizedFrom === dbIntl ||
                                fromLocal === dbLocal || fromIntl === dbIntl) {
                                orderId = order.id;
                                console.log(`[${tenantId}] Matched to order #${orderId} (db: "${order.mobile}")`);
                                break;
                            }
                        }
                    }
                } catch (e) {}
            }

            // Save to Supabase
            try {
                await supabase.from('whatsapp_chats').insert({
                    order_id: orderId,
                    sender_phone: resolvedPhone,
                    message_body: messageBody,
                    direction: 'inbound',
                    created_at: timestamp,
                });
                console.log(`[${tenantId}] SAVED: order=${orderId || 'NULL'} phone=${resolvedPhone} type=${messageType} msg="${messageBody.substring(0, 30)}"`);
            } catch (e) {
                console.error(`[${tenantId}] SAVE FAILED:`, e.message);
            }

            // Notify Laravel
            notifyMessage(tenantId, { from: resolvedPhone, text, messageId: msg.key?.id, timestamp: msg.messageTimestamp });
        }
    });

    sock.ev.on('messages.update', async (updates) => {
        for (const update of updates) {
            if (update.status) {
                notifyStatusUpdate(tenantId, {
                    messageId: update.key?.id,
                    status: update.status,
                });
            }
        }
    });
}

function notifyStatus(tenantId, status) {
    const url = sessions[tenantId]?.webhookUrl || WEBHOOK_URL;
    if (url) {
        try {
            fetch(`${url}/whatsapp-status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tenantId, status }),
            }).catch(() => {});
        } catch (e) {}
    }
}

function notifyMessage(tenantId, message) {
    const url = sessions[tenantId]?.webhookUrl || WEBHOOK_URL;
    if (url) {
        try {
            fetch(`${url}/incoming`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tenantId, message }),
            }).then(r => r.json()).then(d => console.log(`[${tenantId}] Webhook notify:`, d)).catch(() => {});
        } catch (e) {}
    }
}

function notifyStatusUpdate(tenantId, update) {
    const url = sessions[tenantId]?.webhookUrl || WEBHOOK_URL;
    if (url) {
        try {
            fetch(`${url}/whatsapp-status-update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tenantId, ...update }),
            }).catch(() => {});
        } catch (e) {}
    }
}

// ============================================================
// Supabase Real-time: Listen for outbound messages
// ============================================================

function subscribeToOutboundMessages(tenantId, sock) {
    // Clean up existing subscription if any
    unsubscribeFromOutboundMessages(tenantId);

    const channelName = `outbound-${tenantId}`;

    const channel = supabase
        .channel(channelName)
        .on(
            'postgres_changes',
            {
                event: 'INSERT',
                schema: 'public',
                table: 'whatsapp_chats',
            },
            async (payload) => {
                const newRow = payload.new;

                // Only send outbound messages
                if (newRow.direction !== 'outbound') return;

                const phone = newRow.sender_phone;
                const messageBody = newRow.message_body;

                if (!phone || !messageBody) {
                    console.log(`[${tenantId}] Skipping message: missing phone or body`);
                    return;
                }

                // Check if WhatsApp is connected for this tenant
                const session = sessions[tenantId];
                if (!session || session.status !== 'connected' || !session.sock) {
                    console.log(`[${tenantId}] WhatsApp not connected, cannot send to ${phone}`);
                    return;
                }

                try {
                    // Format phone: convert to international format
                    let cleanPhone = phone.replace(/[^0-9]/g, '');
                    const originalPhone = cleanPhone;

                    // Pakistani local format 03xxxxxxxxx → 923xxxxxxxxx
                    if (cleanPhone.startsWith('0') && cleanPhone.length === 11) {
                        cleanPhone = '92' + cleanPhone.substring(1);
                    }
                    // Ensure it starts with country code (92 for Pakistan)
                    if (!cleanPhone.startsWith('92') && cleanPhone.length >= 10) {
                        cleanPhone = '92' + cleanPhone;
                    }
                    // Validate Pakistani mobile number length (should be 12-13 digits)
                    if (cleanPhone.length === 12 && cleanPhone.startsWith('923')) {
                        // 12-digit number missing last digit — likely truncated
                        console.warn(`[${tenantId}] WARNING: Phone "${cleanPhone}" is 12 digits. Pakistani numbers should be 13 digits (e.g. 923XXXXXXXXX). Check if last digit is missing.`);
                    }
                    console.log(`[${tenantId}] Original phone: ${originalPhone} → formatted: ${cleanPhone} (length: ${cleanPhone.length})`);

                    // Try sending with the formatted number
                    const jid = `${cleanPhone}@s.whatsapp.net`;
                    await Promise.race([
                        session.sock.sendMessage(jid, { text: messageBody }),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('Send timeout')), 30000)),
                    ]);
                    console.log(`[${tenantId}] Message sent successfully to: ${cleanPhone}`);

                } catch (err) {
                    console.error(`[${tenantId}] Failed to send to ${phone}: ${err.message}`);
                    console.error(`[${tenantId}] Tip: Check if the phone number in your database matches the full WhatsApp number (e.g. 9233204209946, not 923320420994)`);
                }
            }
        )
        .subscribe((status) => {
            if (status === 'SUBSCRIBED') {
                console.log(`[${tenantId}] Supabase realtime subscribed: watching whatsapp_chats for outbound messages`);
            } else if (status === 'CHANNEL_ERROR') {
                console.error(`[${tenantId}] Supabase realtime channel error`);
            }
        });

    sessions[tenantId].channel = channel;
}

function unsubscribeFromOutboundMessages(tenantId) {
    if (sessions[tenantId]?.channel) {
        supabase.removeChannel(sessions[tenantId].channel);
        sessions[tenantId].channel = null;
        console.log(`[${tenantId}] Unsubscribed from Supabase realtime`);
    }
}

// ============================================================
// API Routes
// ============================================================

// Status
app.get('/api/status/:tenantId', (req, res) => {
    const { tenantId } = req.params;
    const session = sessions[tenantId];
    res.json({
        status: session?.status || 'disconnected',
        qr: session?.qr || null,
        phone: session?.phone || null,
        retries: session?.retries || 0,
    });
});

// QR Code
app.get('/api/qr/:tenantId', (req, res) => {
    const { tenantId } = req.params;
    const session = sessions[tenantId];
    if (!session || !session.qr) {
        return res.json({ qr: null, status: session?.status || 'disconnected' });
    }
    res.json({ qr: session.qr, status: session.status });
});

// Start session
app.post('/api/start/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    try {
        await startSession(tenantId);
        res.json({ success: true, status: sessions[tenantId]?.status || 'connecting' });
    } catch (e) {
        res.status(500).json({ success: false, error: e.message });
    }
});

// Send message
app.post('/api/send/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    const { to, message } = req.body;

    if (!to || !message) {
        return res.status(400).json({ success: false, error: 'Missing "to" or "message"' });
    }

    const session = sessions[tenantId];
    if (!session || session.status !== 'connected') {
        return res.status(400).json({ success: false, error: 'WhatsApp not connected. Scan QR code first.' });
    }

    try {
        // Format phone: convert to international format
        let cleanPhone = to.replace(/[^0-9]/g, '');
        // Pakistani local format 03xxxxxxxxx → 923xxxxxxxxx
        if (cleanPhone.startsWith('0') && cleanPhone.length === 11) {
            cleanPhone = '92' + cleanPhone.substring(1);
        }
        // Ensure it starts with country code (92 for Pakistan)
        if (!cleanPhone.startsWith('92') && cleanPhone.length >= 10) {
            cleanPhone = '92' + cleanPhone;
        }
        console.log(`[API] Sending to: ${to} → formatted: ${cleanPhone} (length: ${cleanPhone.length})`);
        const jid = cleanPhone.includes('@') ? cleanPhone : `${cleanPhone}@s.whatsapp.net`;

        // Send with 30s timeout
        const result = await Promise.race([
            session.sock.sendMessage(jid, { text: message }),
            new Promise((_, reject) => setTimeout(() => reject(new Error('Send timeout (30s)')), 30000)),
        ]);

        res.json({
            success: true,
            messageId: result?.key?.id,
            timestamp: result?.messageTimestamp,
        });
    } catch (e) {
        res.status(500).json({ success: false, error: e.message });
    }
});

// Logout
app.post('/api/logout/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    const session = sessions[tenantId];
    if (session?.sock) {
        try {
            await session.sock.logout();
            session.sock.end(undefined);
        } catch (e) {}
    }
    delete sessions[tenantId];

    // Clean up auth files
    const authPath = path.join(AUTH_DIR, tenantId);
    try {
        fs.rmSync(authPath, { recursive: true, force: true });
    } catch (e) {}

    res.json({ success: true });
});

// Set webhook URL (called by Laravel)
app.post('/api/webhook/:tenantId', (req, res) => {
    const { tenantId } = req.params;
    const { url } = req.body;
    if (!sessions[tenantId]) sessions[tenantId] = { sock: null, status: 'disconnected', qr: null, phone: null, retries: 0 };
    sessions[tenantId].webhookUrl = url;
    res.json({ success: true });
});

// Health check
app.get('/api/health', (req, res) => {
    res.json({
        status: 'running',
        sessions: Object.keys(sessions).length,
        uptime: process.uptime(),
    });
});

// ============================================================
// Start Server
// ============================================================

const server = http.createServer(app);

server.listen(PORT, () => {
    console.log(`[WhatsApp Web Server] Running on port ${PORT}`);
    console.log(`[WhatsApp Web Server] Auth directory: ${AUTH_DIR}`);
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n[WhatsApp Web Server] Shutting down...');
    for (const [tenantId, session] of Object.entries(sessions)) {
        unsubscribeFromOutboundMessages(tenantId);
        if (session.sock) {
            try {
                session.sock.end(undefined);
            } catch (e) {}
        }
    }
    process.exit(0);
});

process.on('SIGTERM', async () => {
    for (const [tenantId, session] of Object.entries(sessions)) {
        unsubscribeFromOutboundMessages(tenantId);
        if (session.sock) {
            try {
                session.sock.end(undefined);
            } catch (e) {}
        }
    }
    process.exit(0);
});
