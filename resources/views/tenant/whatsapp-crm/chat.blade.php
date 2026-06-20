<!DOCTYPE html>
<html lang="en" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Chat - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/dist/umd/supabase.min.js"></script>
    <style>
        * { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #d1d5db; margin: 0; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }

        .chat-bg {
            background-color: #efeae2;
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d1d5db' fill-opacity='0.2'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .contact-active { background: #e9edef !important; }
        .contact-hover:hover { background: #f5f6f6; }

        .status-unfulfilled { background-color: #ffea8a; color: #8a6116; }

        .quick-reply-item:hover { background: #dcf8c6; }
        .quick-reply-item { border-bottom: 1px solid #f3f4f6; }
        .quick-reply-item:last-child { border-bottom: none; }

        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .contact-loading {
            display: flex; align-items: center; justify-content: center;
            padding: 2rem; color: #9ca3af; font-size: 0.75rem; font-weight: 600;
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-1 md:p-4">

<div class="w-full max-w-7xl h-[96vh] bg-white rounded-lg shadow-2xl flex overflow-hidden border border-gray-300">

    <!-- ========== LEFT PANEL: Contacts ========== -->
    <div class="w-[380px] border-r border-gray-300 flex flex-col bg-white shrink-0">

        <!-- Header -->
        <div class="bg-[#f0f2f5] px-4 py-3 flex justify-between items-center border-b border-gray-300 min-h-[60px]">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-700">Chats</h2>
                <span id="contactCount" class="text-[10px] font-bold text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full hidden">0</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="refreshContacts()" id="refreshBtn" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition" title="Refresh contacts">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <a href="/shop/whatsapp-crm" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37"/></svg>
                    Settings
                </a>
                <a href="/shop" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
            </div>
        </div>

        <!-- Search -->
        <div class="p-2 border-b border-gray-200 bg-white">
            <div class="bg-[#f0f2f5] rounded-lg flex items-center px-3 py-1.5">
                <svg class="w-4 h-4 text-gray-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search by name, phone or order..." class="bg-transparent w-full focus:outline-none text-sm" oninput="filterContacts()">
            </div>
        </div>

        <!-- Contacts List (loaded via Supabase) -->
        <div class="flex-1 overflow-y-auto bg-white" id="contactsList">
            <div class="contact-loading" id="contactsLoading">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Loading contacts from Supabase...
                </div>
            </div>
        </div>
    </div>

    <!-- ========== RIGHT PANEL: Chat ========== -->
    <div class="flex-1 flex flex-col bg-[#efeae2] relative min-w-0">

        <!-- Chat Header -->
        <div class="bg-[#f0f2f5] px-4 py-2.5 flex items-center justify-between border-b border-gray-300 min-h-[60px] shadow-sm z-10">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-3 shrink-0" id="chatAvatar">--</div>
                <div class="leading-tight">
                    <h2 class="font-bold text-gray-800 text-[15px]" id="chatName">Select a customer</h2>
                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2" dir="ltr">
                        <span id="chatPhone" class="font-medium text-gray-600">---</span>
                        <span class="text-gray-300">|</span>
                        <span id="chatOrderId" class="font-mono text-[10px] bg-[#e1e6ed] px-1.5 py-0.5 rounded text-gray-600">ID: ---</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="realtimeBadge" class="hidden text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    <span class="inline-block w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse mr-1"></span>Live
                </span>
                <div id="labelContainer" class="hidden">
                    <select id="labelSelect" onchange="changeLabel(this.value)" class="text-xs font-semibold text-white px-3 py-1.5 rounded-full cursor-pointer border-none focus:outline-none shadow-sm max-w-[140px] truncate" dir="ltr">
                        <option value="None" class="bg-white text-gray-700">No Label</option>
                        <option value="In Progress" class="bg-white text-blue-600 font-bold">In Progress</option>
                        <option value="Unfulfilled" class="bg-white text-yellow-600 font-bold">Unfulfilled</option>
                        <option value="Booked" class="bg-white text-purple-600 font-bold">Booked</option>
                        <option value="Delivery Office" class="bg-white text-teal-600 font-bold">Delivery Office</option>
                        <option value="Out For Delivery" class="bg-white text-red-600 font-bold">Out For Delivery</option>
                        <option value="Delivered" class="bg-white text-green-600 font-bold">Delivered</option>
                        <option value="Payment Received" class="bg-white text-emerald-600 font-bold">Payment Received</option>
                        <option value="Returned" class="bg-white text-orange-500 font-bold">Returned</option>
                        <option value="Refused" class="bg-white text-red-800 font-bold">Refused</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 chat-bg flex flex-col" id="chatBox">
            <div class="text-center bg-[#ffeecd] text-gray-600 p-3 rounded-lg text-sm mx-auto my-4 w-3/4 shadow-sm">
                Select a customer from the left to view messages and start chatting.
            </div>
        </div>

        <!-- Quick Reply Menu -->
        <div id="quickReplyMenu" class="hidden absolute bottom-[70px] left-4 right-4 max-w-2xl mx-auto bg-white border border-gray-200 shadow-2xl rounded-xl overflow-hidden z-30" dir="rtl">
            <div class="bg-[#f0f2f5] px-4 py-2 text-xs font-bold text-gray-500 border-b border-gray-200">
                Quick Replies (click to use)
            </div>
            <ul id="quickReplyList" class="max-h-64 overflow-y-auto hide-scrollbar"></ul>
        </div>

        <!-- Message Input -->
        <div class="bg-[#f0f2f5] p-3 px-4 flex items-center gap-3 border-t border-gray-300">
            <button onclick="toggleQuickReplies()" class="text-gray-500 hover:text-gray-700 text-xl shrink-0" title="Quick Replies">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <textarea id="msgInput" rows="1" placeholder="Type a message... (type / for quick replies)" class="flex-1 bg-white rounded-lg px-4 py-2 focus:outline-none border border-white shadow-sm text-sm resize-none min-h-[40px] max-h-[100px]" dir="auto" autocomplete="off" disabled></textarea>
            <button id="sendBtn" onclick="sendMessage()" class="bg-[#128c7e] hover:bg-[#075e54] text-white w-10 h-10 rounded-full flex justify-center items-center shadow-md transition-colors shrink-0" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/></svg>
            </button>
        </div>

    </div>
</div>

<script>
// ============================================================
// State
// ============================================================
let currentOrderId = null;
let currentCustomerPhone = '';
let currentLabel = 'None';
let allContacts = [];
let realtimeSubActive = false;
let pollingInterval = null;
let lastMessageIds = new Set();

const labelColors = {
    'None': 'bg-gray-500 text-white',
    'In Progress': 'bg-[#3b82f6] text-white',
    'Unfulfilled': 'status-unfulfilled font-semibold',
    'Booked': 'bg-[#4b0082] text-white',
    'Delivery Office': 'bg-[#008080] text-white',
    'Out For Delivery': 'bg-[#e74c3c] text-white',
    'Delivered': 'bg-[#2ecc71] text-white',
    'Payment Received': 'bg-[#10b981] text-white',
    'Returned': 'bg-[#e67e22] text-white',
    'Refused': 'bg-[#962d22] text-white',
};

const quickReplies = [
    { title: "Order Booked", text: "Hello! Your order has been successfully booked. \uD83C\uDF89" },
    { title: "Parcel Shipped", text: "Your parcel has been shipped via Pakistan Post. It will arrive in 3-4 business days. \uD83D\uDCE6" },
    { title: "Request Address", text: "Please send your complete delivery address and an alternate mobile number. \uD83D\uDCDD" },
    { title: "Price Info", text: "Hello! Here are our product prices:\n\n\u25E1 1 Pack: Rs. 1,300\n\u25E1 2 Packs: Rs. 2,000\n\u25E1 4 Packs (+1 Free): Rs. 3,500\n\nAll prices include FREE delivery! To order, reply with your quantity." },
    { title: "Guarantee Info", text: "We guarantee results! If you are not satisfied within 7 days, we will refund your money. \uD83E\uDD1D" },
    { title: "Post Office Reminder", text: "Hello!\n\nYour parcel is available at your nearest post office. Please collect it before they return it. \uD83D\uDCE6" },
    { title: "Return Alert", text: "Hello! Your parcel has not been delivered yet and is at risk of being returned. Please collect it immediately. \uD83D\uDCE6\uD83D\uDD19" },
    { title: "Price Objection", text: "I understand your concern. Our product uses premium ingredients which is why it is priced at this level. The results speak for themselves! \uD83D\uDCAF" },
];

// ============================================================
// Supabase Client (CDN)
// ============================================================
const SUPABASE_URL = 'https://zwdumolledeoxlvqckka.supabase.co';
const SUPABASE_KEY = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';
let supabaseClient = null;

function getSupabase() {
    if (supabaseClient) return supabaseClient;
    if (typeof window.supabase === 'undefined') {
        showToast('Supabase SDK failed to load', 'error');
        return null;
    }
    supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY);
    console.log('[WACrm] Supabase client initialized');
    return supabaseClient;
}

// ============================================================
// DB Helpers (inline)
// ============================================================
async function dbFetch(table, { select = '*', filters = {}, order = null, limit = null } = {}) {
    const db = getSupabase();
    let q = db.from(table).select(select);
    for (const [col, val] of Object.entries(filters)) {
        if (val !== undefined && val !== null) q = q.eq(col, val);
    }
    if (order) q = q.order(order.column, { ascending: order.ascending ?? false });
    if (limit) q = q.limit(limit);
    return await q;
}

async function dbInsert(table, rows) {
    const db = getSupabase();
    return await db.from(table).insert(rows).select();
}

async function dbUpdate(table, updates, match) {
    const db = getSupabase();
    return await db.from(table).update(updates).match(match).select();
}

// ============================================================
// Contacts — Fetch from Supabase grouped by unique phone
// ============================================================
async function loadContacts() {
    const { data: orders, error } = await dbFetch('orders', {
        select: 'id, customer, mobile, status, price',
        order: { column: 'id', ascending: false },
    });

    const loading = document.getElementById('contactsLoading');
    const countEl = document.getElementById('contactCount');
    if (loading) loading.style.display = 'none';

    if (error) {
        console.error('[Chat] Error loading contacts:', error);
        showToast('Failed to load contacts: ' + (error.message || 'Unknown error'), 'error');
        return;
    }

    // Group by unique phone — first occurrence = latest order
    const seen = new Set();
    const uniqueContacts = [];
    const phoneCounts = {};

    for (const o of orders) {
        const phone = o.mobile?.trim();
        if (!phone) continue;
        phoneCounts[phone] = (phoneCounts[phone] || 0) + 1;
        if (!seen.has(phone)) {
            seen.add(phone);
            uniqueContacts.push({
                id: o.id,
                customer: o.customer || 'Unknown',
                mobile: phone,
                status: o.status || 'Unfulfilled',
                price: o.price || 0,
                total_orders: 0,
                last_message: null,
                last_message_at: null,
            });
        }
    }
    for (const c of uniqueContacts) c.total_orders = phoneCounts[c.mobile] || 1;

    // Fetch last message per phone
    if (uniqueContacts.length > 0) {
        const phones = uniqueContacts.map(c => c.mobile);
        const { data: lastMsgs } = await dbFetch('whatsapp_chats', {
            select: 'sender_phone, message_body, created_at',
            filters: {},
            order: { column: 'created_at', ascending: false },
        });

        if (lastMsgs) {
            const msgMap = {};
            for (const m of lastMsgs) {
                const np = normalizePhone(m.sender_phone);
                if (np && !msgMap[np]) msgMap[np] = { message: m.message_body, at: m.created_at };
            }
            for (const c of uniqueContacts) {
                const np = normalizePhone(c.mobile);
                const lm = msgMap[np];
                if (lm) { c.last_message = lm.message; c.last_message_at = lm.at; }
            }
        }
    }

    allContacts = uniqueContacts;
    countEl.textContent = allContacts.length;
    countEl.classList.remove('hidden');
    renderContacts(allContacts);
    console.log(`[Chat] Loaded ${allContacts.length} contacts`);
}

// ============================================================
// Chat History — Fetch from Supabase
// ============================================================
async function loadMessages() {
    if (!currentOrderId) return;

    const db = getSupabase();
    console.log('[Chat] loadMessages: orderId=' + currentOrderId + ' phone=' + currentCustomerPhone);

    // Simple query: fetch by order_id
    const { data: messages, error } = await db.from('whatsapp_chats')
        .select('*')
        .eq('order_id', currentOrderId)
        .order('created_at', { ascending: true });

    if (error) {
        console.error('[Chat] Error loading messages:', error);
        showToast('Failed to load messages', 'error');
        return;
    }

    console.log('[Chat] loadMessages: found', (messages || []).length, 'messages');
    if (messages && messages.length > 0) {
        messages.forEach(m => console.log('[Chat] msg id=' + m.id + ' dir=' + m.direction + ' phone=' + m.sender_phone + ' body=' + (m.message_body || '').substring(0, 20)));
    }
    renderMessages(messages || []);
    lastMessageIds = new Set((messages || []).map(m => m.id));
}

// ============================================================
// Send Message — Insert into Supabase
// ============================================================
async function sendMessage() {
    const input = document.getElementById('msgInput');
    const message = input.value.trim();
    if (!message || !currentOrderId) return;

    input.value = '';
    input.style.height = 'auto';

    // Optimistic append
    const chatBox = document.getElementById('chatBox');
    const emptyState = chatBox.querySelector('.text-center');
    if (emptyState) emptyState.remove();

    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    const tempId = 'tmp-' + Date.now();
    const bubble = document.createElement('div');
    bubble.className = 'max-w-[75%] px-4 py-2 rounded-lg shadow-sm mb-2 bg-[#dcf8c6] rounded-tr-none self-end ml-auto';
    bubble.id = tempId;
    bubble.innerHTML = `
        <p class="text-[13px] text-gray-800 leading-relaxed" dir="auto">${escapeHtml(message).replace(/\n/g, '<br>')}</p>
        <p class="text-[10px] text-gray-500 text-right mt-1 flex justify-end items-center">
            ${timeStr} <span class="ml-1 animate-pulse text-gray-400">...</span>
        </p>
    `;
    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;

    // Insert into Supabase for chat history
    const { data, error } = await dbInsert('whatsapp_chats', {
        order_id: currentOrderId,
        sender_phone: currentCustomerPhone,
        message_body: message,
        direction: 'outbound',
    });

    // Send via Node.js WhatsApp server directly (skip realtime - it's unreliable)
    let waSent = false;
    try {
        const resp = await fetch('http://localhost:3001/api/send/{{ $tenantId }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ to: currentCustomerPhone, message: message }),
        });
        const result = await resp.json();
        waSent = result.success;
        console.log('[Chat] WhatsApp send:', result.success ? 'OK' : result.error);
    } catch (waErr) {
        console.error('[Chat] WhatsApp API error:', waErr);
    }

    const el = document.getElementById(tempId);
    if (!el) return;

    const tickEl = el.querySelector('.ml-1');

    if (error) {
        console.error('[Chat] DB error:', error);
        if (tickEl) tickEl.outerHTML = '<svg class="w-4 h-4 text-red-400 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        showToast('Failed to save: ' + (error.message || 'Unknown error'), 'error');
    } else if (!waSent) {
        if (tickEl) tickEl.outerHTML = '<svg class="w-4 h-4 text-yellow-500 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        showToast('Saved but WhatsApp send failed. Check server.', 'error');
    } else {
        if (tickEl) tickEl.outerHTML = '<svg class="w-4 h-4 text-[#53bdeb] ml-1 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-8 8-3-3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M22 7l-8 8-1-1" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
        if (data && data[0] && data[0].created_at) {
            const timeEl = el.querySelector('.text-\\[10px\\]');
            if (timeEl) {
                const serverTime = new Date(data[0].created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                timeEl.innerHTML = serverTime + ' <svg class="w-4 h-4 text-[#53bdeb] ml-1 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-8 8-3-3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M22 7l-8 8-1-1" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
            }
        }
    }
}

// ============================================================
// Label Change — Update Supabase
// ============================================================
function changeLabel(newLabel) {
    currentLabel = newLabel;
    const sel = document.getElementById('labelSelect');
    sel.className = `text-xs font-semibold px-3 py-1.5 rounded-full cursor-pointer border-none focus:outline-none shadow-sm transition-all ${labelColors[newLabel] || 'bg-gray-500 text-white'} max-w-[140px] truncate`;
    if (!currentOrderId) return;

    dbUpdate('orders', { status: newLabel }, { id: currentOrderId }).then(({ error }) => {
        if (error) {
            console.error('[Chat] Label update error:', error);
            showToast('Failed to update label', 'error');
        } else {
            showToast(`Label changed to "${newLabel}"`, 'success');
            loadContacts();
        }
    });
}

// ============================================================
// Init — Load contacts from Supabase on page load
// ============================================================
document.addEventListener('DOMContentLoaded', async () => {
    setupQuickReplies();
    setupInputHandlers();

    const WA = getSupabase();
    if (!WA) return;

    console.log('[Chat] Supabase ready, loading contacts...');
    await loadContacts();

    // Auto-refresh contacts every 15 seconds (lightweight)
    setInterval(loadContacts, 15000);
});

function renderContacts(contacts) {
    const list = document.getElementById('contactsList');

    if (!contacts.length) {
        list.innerHTML = '<div class="contact-loading">No contacts found in Supabase</div>';
        return;
    }

    const labelStyles = {
        'Unfulfilled': 'status-unfulfilled font-semibold',
        'Pending': 'status-unfulfilled font-semibold',
        'Booked': 'bg-[#4b0082] text-white',
        'In Progress': 'bg-[#3b82f6] text-white',
        'Delivery Office': 'bg-[#008080] text-white',
        'Out For Delivery': 'bg-[#e74c3c] text-white',
        'Delivered': 'bg-[#2ecc71] text-white',
        'Payment Received': 'bg-[#10b981] text-white',
        'Returned': 'bg-[#e67e22] text-white',
        'Refused': 'bg-[#962d22] text-white',
    };

    list.innerHTML = contacts.map(c => {
        const initials = (c.customer || 'C').substring(0, 2).toUpperCase();
        const lastMsg = c.last_message
            ? c.last_message.substring(0, 40) + (c.last_message.length > 40 ? '...' : '')
            : 'No messages';
        const badge = labelStyles[c.status] || 'bg-gray-500 text-white';
        const time = c.last_message_at
            ? new Date(c.last_message_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
            : '';

        return `
            <div class="contact-item flex items-center p-3 border-b border-gray-100 cursor-pointer contact-hover transition-colors"
                 data-id="${c.id}"
                 data-customer="${escapeHtml(c.customer)}"
                 data-mobile="${escapeHtml(c.mobile)}"
                 data-status="${escapeHtml(c.status)}"
                 data-price="${c.price}"
                 onclick="openChat(this)">
                <div class="w-12 h-12 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-3 shadow-sm shrink-0 text-sm">
                    ${initials}
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 text-sm truncate">${escapeHtml(c.customer)}</h3>
                        <span class="text-[10px] text-gray-400 shrink-0 font-bold">#${c.id}</span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[11px] text-gray-500 truncate max-w-[180px]">${escapeHtml(lastMsg)}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${badge}">${escapeHtml(c.status)}</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function refreshContacts() {
    const btn = document.getElementById('refreshBtn');
    btn.classList.add('animate-spin');
    loadContacts().then(() => {
        setTimeout(() => btn.classList.remove('animate-spin'), 500);
    });
}

// ============================================================
// Contact Selection — Open Chat
// ============================================================
async function openChat(el) {
    currentOrderId = parseInt(el.dataset.id);
    currentCustomerPhone = el.dataset.mobile;
    currentLabel = el.dataset.status || 'None';

    document.querySelectorAll('.contact-item').forEach(c => c.classList.remove('contact-active'));
    el.classList.add('contact-active');

    document.getElementById('chatAvatar').textContent = (el.dataset.customer || 'C').charAt(0).toUpperCase();
    document.getElementById('chatName').textContent = el.dataset.customer || 'Customer';
    document.getElementById('chatPhone').textContent = el.dataset.mobile || '---';
    document.getElementById('chatOrderId').textContent = 'ID: ' + (el.dataset.id || '---');

    const lc = document.getElementById('labelContainer');
    lc.classList.remove('hidden');

    const sel = document.getElementById('labelSelect');
    const validStatus = labelColors[currentLabel] ? currentLabel : 'None';
    sel.value = validStatus;
    sel.className = `text-xs font-semibold px-3 py-1.5 rounded-full cursor-pointer border-none focus:outline-none shadow-sm transition-all ${labelColors[validStatus] || 'bg-gray-500 text-white'} max-w-[140px] truncate`;

    document.getElementById('msgInput').disabled = false;
    document.getElementById('sendBtn').disabled = false;

    await loadMessages();
    subscribeToOrder(currentOrderId);
}

function renderMessages(messages) {
    const chatBox = document.getElementById('chatBox');
    chatBox.innerHTML = '';

    if (!messages.length) {
        chatBox.innerHTML = '<div class="text-center bg-[#ffeecd] text-gray-600 p-3 rounded-lg text-sm mx-auto my-4 w-3/4 shadow-sm">No messages yet. Send a message to start chatting.</div>';
        return;
    }

    messages.forEach(msg => {
        const isOut = msg.direction === 'outbound';
        const bubble = document.createElement('div');
        bubble.className = `max-w-[75%] px-4 py-2 rounded-lg shadow-sm mb-2 ${isOut ? 'bg-[#dcf8c6] rounded-tr-none self-end ml-auto' : 'bg-white rounded-tl-none self-start'}`;

        const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        const body = msg.message_body ? escapeHtml(msg.message_body).replace(/\n/g, '<br>') : '';
        const tick = isOut ? '<svg class="w-4 h-4 text-[#53bdeb] ml-1 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-8 8-3-3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M22 7l-8 8-1-1" fill="none" stroke="currentColor" stroke-width="2"/></svg>' : '';

        bubble.innerHTML = `
            <p class="text-[13px] text-gray-800 leading-relaxed" dir="auto">${body}</p>
            <p class="text-[10px] ${isOut ? 'text-gray-500' : 'text-gray-400'} text-right mt-1 flex justify-end items-center">
                ${time} ${tick}
            </p>
        `;
        chatBox.appendChild(bubble);
    });

    chatBox.scrollTop = chatBox.scrollHeight;
}

// ============================================================
// Polling Fallback (when Realtime fails)
// ============================================================
function startPollingFallback() {
    if (pollingInterval) return;
    console.log('[Polling] Starting fallback polling every 5s');
    document.getElementById('realtimeBadge').classList.remove('hidden');
    document.getElementById('realtimeBadge').textContent = '⚡ Polling';
    document.getElementById('realtimeBadge').className = 'text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full';

    // Initialize with current message IDs
    const db = getSupabase();
    db.from('whatsapp_chats').select('id')
        .eq('order_id', currentOrderId)
        .then(({ data }) => {
            if (data) data.forEach(m => lastMessageIds.add(m.id));
        });

    pollingInterval = setInterval(async () => {
        if (!currentOrderId || !currentCustomerPhone) return;
        try {
            const db = getSupabase();
            const { data: msgs } = await db.from('whatsapp_chats')
                .select('id')
                .eq('order_id', currentOrderId);

            if (msgs) {
                const newMsgs = msgs.filter(m => !lastMessageIds.has(m.id));
                if (newMsgs.length > 0) {
                    console.log('[Polling] New messages:', newMsgs.length, 'reloading...');
                    await loadMessages();
                    msgs.forEach(m => lastMessageIds.add(m.id));
                }
            }
        } catch (e) {
            console.error('[Polling] Error:', e.message);
        }
    }, 5000);
}

// ============================================================
// Real-time Subscription via Supabase
// ============================================================
let currentSubChannel = null;

function subscribeToOrder(orderId) {
    const db = getSupabase();
    if (!db) return;

    // Unsubscribe from previous
    if (currentSubChannel) {
        db.removeChannel(currentSubChannel);
        currentSubChannel = null;
    }

    realtimeSubActive = false;

    // Subscribe to ALL inserts on whatsapp_chats (no filter)
    // Match client-side to handle order_id=null cases
    currentSubChannel = db
        .channel('chat-realtime-' + Date.now())
        .on(
            'postgres_changes',
            {
                event: 'INSERT',
                schema: 'public',
                table: 'whatsapp_chats',
            },
            (payload) => {
                const row = payload.new;

                // Skip outbound — already shown via optimistic UI
                if (row.direction === 'outbound') {
                    lastMessageIds.add(row.id);
                    return;
                }

                // Skip if already rendered
                if (lastMessageIds.has(row.id)) return;

                // Match by order_id OR by sender_phone
                const matchesOrder = row.order_id && row.order_id === currentOrderId;
                const matchesPhone = currentCustomerPhone &&
                    row.sender_phone && normalizePhone(row.sender_phone) === normalizePhone(currentCustomerPhone);

                if (matchesOrder || matchesPhone) {
                    // If message matched by phone but order_id is null, update it
                    if (matchesPhone && !matchesOrder && row.order_id === null) {
                        db.from('whatsapp_chats').update({ order_id: currentOrderId })
                            .eq('id', row.id).then(() => {});
                        row.order_id = currentOrderId;
                    }
                    appendIncomingMessage(row);
                    lastMessageIds.add(row.id);
                    console.log('[Realtime] New message for order', orderId, 'from', row.sender_phone);
                }
                updateContactPreview(row);
            }
        )
        .subscribe((status) => {
            if (status === 'SUBSCRIBED') {
                realtimeSubActive = true;
                document.getElementById('realtimeBadge').classList.remove('hidden');
                document.getElementById('realtimeBadge').textContent = '🟢 Live';
                document.getElementById('realtimeBadge').className = 'text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full';
                console.log('[Realtime] Subscribed to whatsapp_chats (all inserts)');
                // Stop polling if realtime works
                if (pollingInterval) { clearInterval(pollingInterval); pollingInterval = null; }
            } else if (status === 'CLOSED' || status === 'CHANNEL_ERROR') {
                console.error('[Realtime] Subscription failed:', status);
                // Only start polling after a delay to avoid rapid restarts
                setTimeout(() => {
                    if (!realtimeSubActive) startPollingFallback();
                }, 2000);
            }
        });
}

function appendIncomingMessage(msg) {
    const chatBox = document.getElementById('chatBox');
    const emptyState = chatBox.querySelector('.text-center');
    if (emptyState) emptyState.remove();

    const isOut = msg.direction === 'outbound';
    const bubble = document.createElement('div');
    bubble.className = `max-w-[75%] px-4 py-2 rounded-lg shadow-sm mb-2 ${isOut ? 'bg-[#dcf8c6] rounded-tr-none self-end ml-auto' : 'bg-white rounded-tl-none self-start'}`;

    const time = new Date(msg.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    const body = msg.message_body ? escapeHtml(msg.message_body).replace(/\n/g, '<br>') : '';
    const tick = isOut ? '<svg class="w-4 h-4 text-[#53bdeb] ml-1 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-8 8-3-3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M22 7l-8 8-1-1" fill="none" stroke="currentColor" stroke-width="2"/></svg>' : '';

    bubble.innerHTML = `
        <p class="text-[13px] text-gray-800 leading-relaxed" dir="auto">${body}</p>
        <p class="text-[10px] ${isOut ? 'text-gray-500' : 'text-gray-400'} text-right mt-1 flex justify-end items-center">
            ${time} ${tick}
        </p>
    `;

    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function updateContactPreview(msg) {
    const normalized = normalizePhone(msg.sender_phone);
    document.querySelectorAll('.contact-item').forEach(el => {
        if (normalizePhone(el.dataset.mobile) === normalized) {
            const msgSpan = el.querySelector('.text-\\[11px\\]');
            if (msgSpan) {
                const preview = msg.message_body
                    ? msg.message_body.substring(0, 40) + (msg.message_body.length > 40 ? '...' : '')
                    : 'No messages';
                msgSpan.textContent = preview;
            }
        }
    });
}

// ============================================================
// Quick Replies
// ============================================================
function setupQuickReplies() {
    const list = document.getElementById('quickReplyList');
    list.innerHTML = '';
    quickReplies.forEach(reply => {
        const li = document.createElement('li');
        li.className = 'quick-reply-item px-4 py-2.5 cursor-pointer transition-colors';
        li.innerHTML = `<strong class="text-sm text-gray-800 block">${escapeHtml(reply.title)}</strong><span class="text-xs text-gray-500 block mt-0.5" style="direction:ltr;text-align:left">${escapeHtml(reply.text.substring(0, 60))}${reply.text.length > 60 ? '...' : ''}</span>`;
        li.onclick = () => {
            document.getElementById('msgInput').value = reply.text;
            document.getElementById('quickReplyMenu').classList.add('hidden');
            document.getElementById('msgInput').focus();
        };
        list.appendChild(li);
    });
}

function toggleQuickReplies() {
    document.getElementById('quickReplyMenu').classList.toggle('hidden');
}

document.addEventListener('click', (e) => {
    const menu = document.getElementById('quickReplyMenu');
    const btn = e.target.closest('button');
    if (btn && btn.onclick && btn.onclick.toString().includes('toggleQuickReplies')) return;
    if (!menu.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// ============================================================
// Search Contacts
// ============================================================
function filterContacts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.contact-item').forEach(el => {
        const name = (el.dataset.customer || '').toLowerCase();
        const phone = (el.dataset.mobile || '').toLowerCase();
        const id = (el.dataset.id || '').toString();
        el.style.display = (name.includes(q) || phone.includes(q) || id.includes(q)) ? '' : 'none';
    });
}

// ============================================================
// Toast
// ============================================================
function showToast(msg, type) {
    const existing = document.querySelector('.wa-toast');
    if (existing) existing.remove();
    const colors = { success: 'bg-emerald-600', warning: 'bg-amber-500', error: 'bg-rose-500' };
    const toast = document.createElement('div');
    toast.className = `wa-toast fixed top-4 right-4 z-50 ${colors[type] || 'bg-slate-700'} text-white text-xs font-bold px-4 py-3 rounded-xl shadow-2xl max-w-xs`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ============================================================
// Utility
// ============================================================
function normalizePhone(phone) {
    if (!phone) return '';
    let p = phone.replace(/[^0-9]/g, '');
    if (p.startsWith('0') && p.length === 11) p = '92' + p.substring(1);
    if (!p.startsWith('92') && p.length >= 10) p = '92' + p;
    return p;
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ============================================================
// Input Handlers
// ============================================================
function setupInputHandlers() {
    document.getElementById('msgInput').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';

        if (this.value === '/') {
            document.getElementById('quickReplyMenu').classList.remove('hidden');
        } else if (!this.value.includes('/')) {
            document.getElementById('quickReplyMenu').classList.add('hidden');
        }
    });

    document.getElementById('msgInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

// ============================================================
// Cleanup on page unload
// ============================================================
window.addEventListener('beforeunload', () => {
    const db = getSupabase();
    if (db && currentSubChannel) {
        db.removeChannel(currentSubChannel);
    }
});
</script>

</body>
</html>
