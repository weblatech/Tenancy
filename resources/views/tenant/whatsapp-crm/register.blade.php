<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connect WhatsApp - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%); min-height: 100vh; }
        .glass { background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.6); }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { border-color: #25D366; box-shadow: 0 0 0 4px rgba(37, 211, 102, 0.15); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUp 0.4s ease-out; }
        @keyframes pulse-green { 0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 50% { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0); } }
        .pulse-green { animation: pulse-green 2s infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-lg">

    <!-- Header -->
    <div class="text-center mb-8 animate-slide-up">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-3xl shadow-lg shadow-green-500/30 mb-4 pulse-green">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </div>
        <h1 class="text-2xl font-black text-gray-900">Connect Your WhatsApp</h1>
        <p class="text-gray-500 text-sm mt-1">Select your existing number or register a new one</p>
    </div>

    <!-- Already Connected Banner -->
    @if(!empty($phoneNumberId))
    <div class="glass rounded-3xl p-6 shadow-xl mb-6 animate-slide-up border-2 border-green-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-green-800">WhatsApp Already Connected</p>
                <p class="text-[10px] text-green-600">Phone Number ID: {{ $phoneNumberId }}</p>
            </div>
            <a href="/shop/whatsapp-chat" class="bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition">Go to Chat</a>
        </div>
    </div>
    @endif

    <!-- Step 1: Select Existing Number -->
    <div id="step1" class="glass rounded-3xl p-8 shadow-xl animate-slide-up" style="animation-delay: 0.2s">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-2xl mb-3">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <h2 class="text-lg font-black text-gray-900">Your WhatsApp Numbers</h2>
            <p class="text-gray-500 text-xs mt-1">Select a number from your WhatsApp Business Account</p>
        </div>

        @if(!$isReady)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-6">
            <p class="text-xs text-amber-800 font-bold">Platform WhatsApp not configured</p>
            <p class="text-[10px] text-amber-700 mt-1">Ask your admin to set up the WhatsApp Business API first at <a href="/admin/whatsapp-provider" class="underline font-bold">/admin/whatsapp-provider</a></p>
        </div>
        @endif

        <!-- Loading state -->
        <div id="loadingNumbers" class="text-center py-8">
            <svg class="w-8 h-8 animate-spin text-green-500 mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <p class="text-sm text-gray-500 font-bold">Loading your numbers...</p>
        </div>

        <!-- Numbers list -->
        <div id="numbersList" class="space-y-3 hidden"></div>

        <!-- No numbers found -->
        <div id="noNumbers" class="hidden text-center py-6">
            <p class="text-sm text-gray-500 mb-4">No phone numbers found in your WhatsApp Business Account.</p>
            <p class="text-xs text-gray-400">You can enter your Phone Number ID manually in <a href="/shop/whatsapp-crm" class="text-green-600 font-bold hover:underline">CRM Settings</a>.</p>
        </div>

        <!-- Error -->
        <div id="step1Error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-bold"></div>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-[10px] text-gray-400 font-bold uppercase">or enter manually</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Manual entry -->
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-black text-gray-500 mb-2 uppercase tracking-wider">Phone Number ID</label>
                <input type="text" id="manualPnId" placeholder="e.g. 1172546945946113" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 input-focus outline-none placeholder-gray-400">
                <p class="text-[10px] text-gray-400 mt-1">From Meta Dashboard > WhatsApp > API Setup</p>
            </div>
            <button onclick="selectManualNumber()" id="manualBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black text-sm py-3 rounded-xl transition shadow-lg shadow-blue-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                Connect This Number
            </button>
        </div>
    </div>

    <!-- Step 2: Success -->
    <div id="step2" class="glass rounded-3xl p-8 shadow-xl hidden animate-slide-up">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-full mb-4 shadow-lg shadow-green-500/30">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-black text-gray-900 mb-2">WhatsApp Connected!</h2>
            <p class="text-gray-500 text-sm mb-6">Your phone number is now active for sending messages</p>

            <div class="bg-gray-50 rounded-2xl p-4 mb-6 text-left">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-gray-500 font-bold">Phone Number</span>
                    <span class="text-sm font-black text-gray-900" id="connectedPhone">---</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-gray-500 font-bold">Status</span>
                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">Active</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-bold">Phone Number ID</span>
                    <span class="text-xs font-mono text-gray-600" id="connectedPnId">---</span>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="/shop/whatsapp-crm" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm py-3 rounded-xl transition text-center">CRM Settings</a>
                <a href="/shop/whatsapp-chat" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold text-sm py-3 rounded-xl transition text-center shadow-lg shadow-green-600/20">Start Chatting</a>
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div class="text-center mt-6">
        <a href="/shop" class="text-xs text-gray-400 font-bold hover:text-gray-600 transition">&larr; Back to Dashboard</a>
    </div>
</div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

document.addEventListener('DOMContentLoaded', () => {
    loadExistingNumbers();
});

async function loadExistingNumbers() {
    try {
        const resp = await fetch('/shop/whatsapp-register/list-numbers');
        const data = await resp.json();

        document.getElementById('loadingNumbers').classList.add('hidden');

        if (data.success && data.phone_numbers && data.phone_numbers.length > 0) {
            const list = document.getElementById('numbersList');
            list.innerHTML = data.phone_numbers.map(pn => {
                const phone = pn.display_phone_number || pn.phone_number || 'Unknown';
                const name = pn.verified_name || 'Unnamed';
                const status = pn.status || 'unknown';
                const pnId = pn.id || '';
                const quality = pn.quality_rating || '';

                return `
                    <div class="flex items-center p-4 bg-white border border-gray-200 rounded-2xl hover:border-green-300 hover:bg-green-50/50 transition cursor-pointer group" onclick="selectExistingNumber('${pnId}', '${phone}', '${name}')">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center shrink-0 mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-gray-900 truncate">${name}</p>
                            <p class="text-xs text-gray-500">${phone}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">ID: ${pnId}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${status === 'CONNECTED' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">${status}</span>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>`;
            }).join('');
            list.classList.remove('hidden');
        } else if (data.success && (!data.phone_numbers || data.phone_numbers.length === 0)) {
            document.getElementById('noNumbers').classList.remove('hidden');
        } else {
            document.getElementById('noNumbers').classList.remove('hidden');
            if (data.error) {
                showError('step1Error', data.error);
            }
        }
    } catch (e) {
        document.getElementById('loadingNumbers').classList.add('hidden');
        document.getElementById('noNumbers').classList.remove('hidden');
    }
}

async function selectExistingNumber(pnId, phone, name) {
    try {
        const resp = await fetch('/shop/whatsapp-register/select-number', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ phone_number_id: pnId }),
        });
        const data = await resp.json();

        if (data.success) {
            document.getElementById('connectedPhone').textContent = data.phone_number || phone;
            document.getElementById('connectedPnId').textContent = data.phone_number_id || pnId;
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        } else {
            showError('step1Error', data.error || 'Failed to connect number');
        }
    } catch (e) {
        showError('step1Error', 'Network error: ' + e.message);
    }
}

async function selectManualNumber() {
    const pnId = document.getElementById('manualPnId').value.trim();
    if (!pnId) {
        showError('step1Error', 'Please enter a Phone Number ID');
        return;
    }

    const btn = document.getElementById('manualBtn');
    btn.disabled = true;
    btn.textContent = 'Connecting...';

    try {
        const resp = await fetch('/shop/whatsapp-register/select-number', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ phone_number_id: pnId }),
        });
        const data = await resp.json();

        if (data.success) {
            document.getElementById('connectedPhone').textContent = data.phone_number || 'N/A';
            document.getElementById('connectedPnId').textContent = pnId;
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        } else {
            showError('step1Error', data.error || 'Failed to connect number');
            btn.disabled = false;
            btn.textContent = 'Connect This Number';
        }
    } catch (e) {
        showError('step1Error', 'Network error: ' + e.message);
        btn.disabled = false;
        btn.textContent = 'Connect This Number';
    }
}

function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 8000);
}
</script>

</body>
</html>
