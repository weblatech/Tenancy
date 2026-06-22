<!DOCTYPE html>
<html lang="en" translate="no">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhatsApp Chat - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .modal-backdrop { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-1 md:p-4">

<div class="w-full max-w-7xl h-[96vh] bg-white rounded-lg shadow-2xl flex overflow-hidden border border-gray-300">

    <!-- LEFT PANEL: Contacts -->
    <div class="w-[380px] border-r border-gray-300 flex flex-col bg-white shrink-0">
        <div class="bg-[#f0f2f5] px-4 py-3 flex justify-between items-center border-b border-gray-300 min-h-[60px]">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-bold text-gray-700">Chats</h2>
                <span id="contactCount" class="text-[10px] font-bold text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full hidden">0</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="showNewChatModal()" class="text-gray-500 hover:text-green-600 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition" title="New Chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
                <button onclick="refreshContacts()" id="refreshBtn" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
                <a href="/shop/whatsapp-crm" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition">Settings</a>
                <a href="/shop" class="text-gray-500 hover:text-gray-700 text-xs font-bold flex items-center gap-1 px-2 py-1 rounded hover:bg-gray-200 transition">Dashboard</a>
            </div>
        </div>

        <div class="p-2 border-b border-gray-200 bg-white">
            <div class="bg-[#f0f2f5] rounded-lg flex items-center px-3 py-1.5">
                <svg class="w-4 h-4 text-gray-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="searchInput" placeholder="Search by name or phone..." class="bg-transparent w-full focus:outline-none text-sm" oninput="filterContacts()">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto bg-white" id="contactsList">
            <div class="flex items-center justify-center p-8 text-gray-400 text-sm font-medium" id="contactsLoading">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Loading chats...
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Chat -->
    <div class="flex-1 flex flex-col bg-[#efeae2] relative min-w-0">
        <div class="bg-[#f0f2f5] px-4 py-2.5 flex items-center justify-between border-b border-gray-300 min-h-[60px] shadow-sm z-10">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-3 shrink-0" id="chatAvatar">--</div>
                <div class="leading-tight">
                    <h2 class="font-bold text-gray-800 text-[15px]" id="chatName">Select a customer</h2>
                    <div class="text-xs text-gray-500 mt-0.5" dir="ltr">
                        <span id="chatPhone" class="font-medium text-gray-600">---</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="apiStatus" class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isConfigured ? 'text-emerald-600 bg-emerald-50 border border-emerald-200' : 'text-rose-600 bg-rose-50 border border-rose-200' }}">
                    {{ $isConfigured ? 'API Connected' : 'API Not Set' }}
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 chat-bg flex flex-col" id="chatBox">
            <div class="text-center bg-[#ffeecd] text-gray-600 p-3 rounded-lg text-sm mx-auto my-4 w-3/4 shadow-sm">
                Select a customer from the left to start chatting.
            </div>
        </div>

        <div id="quickReplyMenu" class="hidden absolute bottom-[70px] left-4 right-4 max-w-2xl mx-auto bg-white border border-gray-200 shadow-2xl rounded-xl overflow-hidden z-30">
            <div class="bg-[#f0f2f5] px-4 py-2 text-xs font-bold text-gray-500 border-b border-gray-200">Quick Replies (click to use)</div>
            <ul id="quickReplyList" class="max-h-64 overflow-y-auto hide-scrollbar"></ul>
        </div>

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

<!-- New Chat Modal -->
<div id="newChatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center modal-backdrop">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Start New Chat</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Customer Name</label>
                <input type="text" id="newChatName" placeholder="Enter name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Phone Number</label>
                <input type="text" id="newChatPhone" placeholder="03XXXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none" dir="ltr">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <button onclick="closeNewChatModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition">Cancel</button>
            <button onclick="createNewChat()" class="px-6 py-2 text-sm font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl transition shadow-md">Start Chat</button>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
let currentConversationId = null;
let allContacts = [];
let pollInterval = null;

const quickReplies = [
    { title: "Order Booked", text: "Hello! Your order has been successfully booked. \uD83C\uDF89" },
    { title: "Parcel Shipped", text: "Your parcel has been shipped. It will arrive in 3-4 business days. \uD83D\uDCE6" },
    { title: "Request Address", text: "Please send your complete delivery address and an alternate mobile number. \uD83D\uDCDD" },
    { title: "Post Office Reminder", text: "Hello!\n\nYour parcel is available at your nearest post office. Please collect it before they return it. \uD83D\uDCE6" },
    { title: "Return Alert", text: "Hello! Your parcel has not been delivered yet and is at risk of being returned. Please collect it immediately. \uD83D\uDCE6\uD83D\uDD19" },
];

// Load contacts from local DB
async function loadContacts() {
    try {
        const resp = await fetch('/shop/whatsapp-chat/contacts');
        const data = await resp.json();
        allContacts = data.contacts || [];

        document.getElementById('contactsLoading').style.display = 'none';
        const countEl = document.getElementById('contactCount');
        countEl.textContent = allContacts.length;
        countEl.classList.remove('hidden');

        renderContacts(allContacts);
    } catch (e) {
        console.error('Load contacts error:', e);
        document.getElementById('contactsLoading').innerHTML = '<span class="text-red-400">Failed to load</span>';
    }
}

function renderContacts(contacts) {
    const list = document.getElementById('contactsList');
    if (!contacts.length) {
        list.innerHTML = '<div class="flex items-center justify-center p-8 text-gray-400 text-sm">No conversations yet. Click + to start one.</div>';
        return;
    }

    list.innerHTML = contacts.map(c => {
        const initials = (c.customer_name || 'C').substring(0, 2).toUpperCase();
        const lastMsg = c.last_message ? (c.last_message.substring(0, 45) + (c.last_message.length > 45 ? '...' : '')) : 'No messages yet';
        const time = c.last_message_at ? new Date(c.last_message_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : '';
        const isActive = c.id == currentConversationId;

        return `
            <div class="contact-item flex items-center p-3 border-b border-gray-100 cursor-pointer contact-hover transition-colors ${isActive ? 'contact-active' : ''}"
                 data-id="${c.id}" data-name="${escapeHtml(c.customer_name)}" data-phone="${escapeHtml(c.customer_phone)}" data-status="${escapeHtml(c.status)}"
                 onclick="openChat(${c.id}, '${escapeHtml(c.customer_name)}', '${escapeHtml(c.customer_phone)}', '${escapeHtml(c.status || 'open')}')">
                <div class="w-12 h-12 bg-teal-600 rounded-full flex items-center justify-center text-white font-bold mr-3 shadow-sm shrink-0 text-sm">${initials}</div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800 text-sm truncate">${escapeHtml(c.customer_name)}</h3>
                        <span class="text-[10px] text-gray-400 shrink-0 font-bold">${time}</span>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[11px] text-gray-500 truncate max-w-[200px]">${escapeHtml(lastMsg)}</span>
                        ${c.status && c.status !== 'open' ? `<span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700">${escapeHtml(c.status)}</span>` : ''}
                    </div>
                </div>
            </div>`;
    }).join('');
}

function refreshContacts() {
    const btn = document.getElementById('refreshBtn');
    btn.classList.add('animate-spin');
    loadContacts().then(() => setTimeout(() => btn.classList.remove('animate-spin'), 500));
}

async function openChat(convId, name, phone, status) {
    currentConversationId = convId;

    document.querySelectorAll('.contact-item').forEach(c => c.classList.remove('contact-active'));
    event.currentTarget?.classList.add('contact-active');

    document.getElementById('chatAvatar').textContent = (name || 'C').charAt(0).toUpperCase();
    document.getElementById('chatName').textContent = name || 'Customer';
    document.getElementById('chatPhone').textContent = phone || '---';
    document.getElementById('msgInput').disabled = false;
    document.getElementById('sendBtn').disabled = false;

    await loadMessages(convId);
    startPolling(convId);
}

async function loadMessages(convId) {
    try {
        const resp = await fetch(`/shop/whatsapp-chat/${convId}/messages`);
        const data = await resp.json();
        renderMessages(data.messages || []);
    } catch (e) {
        console.error('Load messages error:', e);
    }
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
            <p class="text-[10px] ${isOut ? 'text-gray-500' : 'text-gray-400'} text-right mt-1 flex justify-end items-center">${time} ${tick}</p>`;
        chatBox.appendChild(bubble);
    });

    chatBox.scrollTop = chatBox.scrollHeight;
}

async function sendMessage() {
    const input = document.getElementById('msgInput');
    const message = input.value.trim();
    if (!message || !currentConversationId) return;

    input.value = '';
    input.style.height = 'auto';

    // Optimistic UI
    const chatBox = document.getElementById('chatBox');
    const emptyState = chatBox.querySelector('.text-center');
    if (emptyState) emptyState.remove();

    const now = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    const tempId = 'tmp-' + Date.now();
    const bubble = document.createElement('div');
    bubble.className = 'max-w-[75%] px-4 py-2 rounded-lg shadow-sm mb-2 bg-[#dcf8c6] rounded-tr-none self-end ml-auto';
    bubble.id = tempId;
    bubble.innerHTML = `
        <p class="text-[13px] text-gray-800 leading-relaxed" dir="auto">${escapeHtml(message).replace(/\n/g, '<br>')}</p>
        <p class="text-[10px] text-gray-500 text-right mt-1 flex justify-end items-center">${now} <span class="ml-1 animate-pulse text-gray-400">...</span></p>`;
    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
        const resp = await fetch(`/shop/whatsapp-chat/${currentConversationId}/send`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ message }),
        });
        const result = await resp.json();

        const el = document.getElementById(tempId);
        if (!el) return;
        const tickEl = el.querySelector('.ml-1');

        if (result.success) {
            if (tickEl) tickEl.outerHTML = '<svg class="w-4 h-4 text-[#53bdeb] ml-1 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M18 7l-8 8-3-3" fill="none" stroke="currentColor" stroke-width="2"/><path d="M22 7l-8 8-1-1" fill="none" stroke="currentColor" stroke-width="2"/></svg>';
        } else {
            if (tickEl) tickEl.outerHTML = '<svg class="w-4 h-4 text-yellow-500 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            let errMsg = result.error || 'Send failed';
            if (result.hint) errMsg += '\n' + result.hint;
            showToast(errMsg, 'error');
        }
    } catch (e) {
        showToast('Send failed: ' + e.message, 'error');
    }

    refreshContacts();
}

function startPolling(convId) {
    if (pollInterval) clearInterval(pollInterval);
    let lastCount = 0;

    pollInterval = setInterval(async () => {
        if (currentConversationId !== convId) return;
        try {
            const resp = await fetch(`/shop/whatsapp-chat/${convId}/messages`);
            const data = await resp.json();
            const msgs = data.messages || [];
            if (msgs.length !== lastCount) {
                lastCount = msgs.length;
                renderMessages(msgs);
            }
        } catch (e) {}
    }, 5000);
}

function showNewChatModal() { document.getElementById('newChatModal').classList.remove('hidden'); }
function closeNewChatModal() { document.getElementById('newChatModal').classList.add('hidden'); }

async function createNewChat() {
    const name = document.getElementById('newChatName').value.trim();
    const phone = document.getElementById('newChatPhone').value.trim();
    if (!name || !phone) { showToast('Please enter name and phone', 'error'); return; }

    try {
        const resp = await fetch('/shop/whatsapp-chat/new', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ customer_name: name, customer_phone: phone }),
        });
        const result = await resp.json();
        if (result.success) {
            closeNewChatModal();
            document.getElementById('newChatName').value = '';
            document.getElementById('newChatPhone').value = '';
            await loadContacts();
            openChat(result.conversation_id, name, phone, 'open');
        }
    } catch (e) {
        showToast('Failed: ' + e.message, 'error');
    }
}

function setupQuickReplies() {
    const list = document.getElementById('quickReplyList');
    list.innerHTML = '';
    quickReplies.forEach(reply => {
        const li = document.createElement('li');
        li.className = 'px-4 py-2.5 cursor-pointer hover:bg-green-50 transition-colors border-b border-gray-100 last:border-b-0';
        li.innerHTML = `<strong class="text-sm text-gray-800 block">${escapeHtml(reply.title)}</strong><span class="text-xs text-gray-500 block mt-0.5">${escapeHtml(reply.text.substring(0, 60))}${reply.text.length > 60 ? '...' : ''}</span>`;
        li.onclick = () => {
            document.getElementById('msgInput').value = reply.text;
            document.getElementById('quickReplyMenu').classList.add('hidden');
            document.getElementById('msgInput').focus();
        };
        list.appendChild(li);
    });
}

function toggleQuickReplies() { document.getElementById('quickReplyMenu').classList.toggle('hidden'); }

document.addEventListener('click', (e) => {
    const menu = document.getElementById('quickReplyMenu');
    if (!menu.contains(e.target) && !e.target.closest('button[onclick*="toggleQuickReplies"]')) {
        menu.classList.add('hidden');
    }
});

function filterContacts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.contact-item').forEach(el => {
        const name = (el.dataset.name || '').toLowerCase();
        const phone = (el.dataset.phone || '').toLowerCase();
        el.style.display = (name.includes(q) || phone.includes(q)) ? '' : 'none';
    });
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showToast(msg, type) {
    const existing = document.querySelector('.wa-toast');
    if (existing) existing.remove();
    const colors = { success: 'bg-emerald-600', warning: 'bg-amber-500', error: 'bg-rose-500' };
    const toast = document.createElement('div');
    toast.className = `wa-toast fixed top-4 right-4 z-50 ${colors[type] || 'bg-slate-700'} text-white text-[11px] font-bold px-4 py-3 rounded-xl shadow-2xl max-w-sm whitespace-pre-line`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 5000);
}

document.getElementById('msgInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    if (this.value === '/') document.getElementById('quickReplyMenu').classList.remove('hidden');
    else if (!this.value.includes('/')) document.getElementById('quickReplyMenu').classList.add('hidden');
});

document.getElementById('msgInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

document.addEventListener('DOMContentLoaded', () => {
    setupQuickReplies();
    loadContacts();
});
</script>

</body>
</html>
