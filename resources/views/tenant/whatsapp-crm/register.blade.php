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
        .step-active { background: #16a34a; color: white; }
        .step-done { background: #15803d; color: white; }
        .step-pending { background: #e5e7eb; color: #9ca3af; }
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
        <p class="text-gray-500 text-sm mt-1">Register your phone number to start sending messages</p>
    </div>

    <!-- Progress Steps -->
    <div class="flex items-center justify-center gap-2 mb-8 animate-slide-up" style="animation-delay: 0.1s">
        <div class="flex items-center gap-2">
            <div id="step1Dot" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-active">1</div>
            <span class="text-xs font-bold text-gray-600">Phone</span>
        </div>
        <div class="w-12 h-0.5 bg-gray-200 mx-1" id="step1Line"></div>
        <div class="flex items-center gap-2">
            <div id="step2Dot" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-pending">2</div>
            <span class="text-xs font-bold text-gray-400" id="step2Label">Verify</span>
        </div>
        <div class="w-12 h-0.5 bg-gray-200 mx-1" id="step2Line"></div>
        <div class="flex items-center gap-2">
            <div id="step3Dot" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-pending">3</div>
            <span class="text-xs font-bold text-gray-400" id="step3Label">Done</span>
        </div>
    </div>

    <!-- Step 1: Enter Phone Number -->
    <div id="step1" class="glass rounded-3xl p-8 shadow-xl animate-slide-up" style="animation-delay: 0.2s">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-2xl mb-3">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <h2 class="text-lg font-black text-gray-900">Enter Your WhatsApp Number</h2>
            <p class="text-gray-500 text-xs mt-1">We'll send a verification code to this number</p>
        </div>

        @if(!$isReady)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-6">
            <p class="text-xs text-amber-800 font-bold">Platform WhatsApp not configured</p>
            <p class="text-[10px] text-amber-700 mt-1">Ask your admin to set up the WhatsApp Business API first.</p>
        </div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-gray-500 mb-2 uppercase tracking-wider">Your Name (for display)</label>
                <input type="text" id="storeName" value="{{ $storeName }}" placeholder="Store Name" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 input-focus outline-none placeholder-gray-400">
            </div>
            <div>
                <label class="block text-xs font-black text-gray-500 mb-2 uppercase tracking-wider">WhatsApp Phone Number <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <div class="w-20 px-3 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold text-gray-600 text-center">+92</div>
                    <input type="text" id="phoneInput" placeholder="3XXXXXXXXX" maxlength="10" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-700 input-focus outline-none placeholder-gray-400" dir="ltr">
                </div>
                <p class="text-[10px] text-gray-400 mt-1.5">Enter without country code. Example: 3001234567</p>
            </div>
        </div>

        <button onclick="sendOTP()" id="sendOtpBtn" class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-black text-sm py-3.5 rounded-xl transition shadow-lg shadow-green-600/20 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" {{ !$isReady ? 'disabled' : '' }}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/></svg>
            Send Verification Code
        </button>

        <div id="step1Error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-bold"></div>
    </div>

    <!-- Step 2: Enter OTP -->
    <div id="step2" class="glass rounded-3xl p-8 shadow-xl hidden animate-slide-up">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-green-100 rounded-2xl mb-3">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="text-lg font-black text-gray-900">Enter Verification Code</h2>
            <p class="text-gray-500 text-xs mt-1">A 6-digit code was sent to <span id="otpPhoneDisplay" class="font-bold text-gray-700"></span></p>
        </div>

        <div class="flex justify-center gap-3 mb-6">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-black border-2 border-gray-200 rounded-xl input-focus outline-none" inputmode="numeric">
        </div>

        <button onclick="verifyOTP()" id="verifyBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-black text-sm py-3.5 rounded-xl transition shadow-lg shadow-green-600/20 flex items-center justify-center gap-2 disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Verify & Connect
        </button>

        <div class="text-center mt-4">
            <button onclick="resendOTP()" id="resendBtn" class="text-xs text-green-600 font-bold hover:underline" disabled>Resend Code in <span id="resendTimer">30</span>s</button>
        </div>

        <div id="step2Error" class="hidden mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-bold"></div>
    </div>

    <!-- Step 3: Success -->
    <div id="step3" class="glass rounded-3xl p-8 shadow-xl hidden animate-slide-up">
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
        <a href="/shop" class="text-xs text-gray-400 font-bold hover:text-gray-600 transition">← Back to Dashboard</a>
    </div>
</div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let phoneNumberId = null;
let countdownInterval = null;

// OTP Input auto-focus
document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
    input.addEventListener('input', (e) => {
        const val = e.target.value;
        if (val && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        if (index === inputs.length - 1 && val) {
            verifyOTP();
        }
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        paste.split('').forEach((char, i) => {
            if (inputs[i]) inputs[i].value = char;
        });
        if (paste.length === 6) {
            verifyOTP();
        }
    });
});

async function sendOTP() {
    const name = document.getElementById('storeName').value.trim();
    const phone = document.getElementById('phoneInput').value.trim();

    if (!phone || phone.length < 10) {
        showError('step1Error', 'Please enter a valid 10-digit phone number');
        return;
    }

    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Sending...';

    try {
        const resp = await fetch('/shop/whatsapp-register/send-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ phone_number: '92' + phone, store_name: name }),
        });
        const data = await resp.json();

        if (data.success) {
            phoneNumberId = data.phone_number_id;
            document.getElementById('otpPhoneDisplay').textContent = '+92 ' + phone;
            goToStep(2);
            startResendTimer();
            document.querySelectorAll('.otp-input')[0].focus();
        } else {
            showError('step1Error', data.error || 'Failed to send verification code');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/></svg> Send Verification Code';
        }
    } catch (e) {
        showError('step1Error', 'Network error: ' + e.message);
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/></svg> Send Verification Code';
    }
}

async function verifyOTP() {
    const inputs = document.querySelectorAll('.otp-input');
    const code = Array.from(inputs).map(i => i.value).join('');

    if (code.length !== 6) {
        showError('step2Error', 'Please enter the complete 6-digit code');
        return;
    }

    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Verifying...';

    try {
        const resp = await fetch('/shop/whatsapp-register/verify-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ phone_number_id: phoneNumberId, code: code }),
        });
        const data = await resp.json();

        if (data.success) {
            document.getElementById('connectedPhone').textContent = data.phone_number || '+92 ' + document.getElementById('phoneInput').value;
            document.getElementById('connectedPnId').textContent = phoneNumberId;
            goToStep(3);
        } else {
            showError('step2Error', data.error || 'Invalid code. Please try again.');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Verify & Connect';
            inputs.forEach(i => { i.value = ''; });
            inputs[0].focus();
        }
    } catch (e) {
        showError('step2Error', 'Network error: ' + e.message);
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Verify & Connect';
    }
}

function resendOTP() {
    sendOTP();
    startResendTimer();
}

function startResendTimer() {
    let seconds = 30;
    const btn = document.getElementById('resendBtn');
    const timer = document.getElementById('resendTimer');
    btn.disabled = true;
    btn.style.color = '#9ca3af';

    if (countdownInterval) clearInterval(countdownInterval);
    countdownInterval = setInterval(() => {
        seconds--;
        timer.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(countdownInterval);
            btn.disabled = false;
            btn.style.color = '';
            btn.textContent = 'Resend Code';
        }
    }, 1000);
}

function goToStep(step) {
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step3').classList.add('hidden');
    document.getElementById('step' + step).classList.remove('hidden');

    for (let i = 1; i <= 3; i++) {
        const dot = document.getElementById('step' + i + 'Dot');
        const label = document.getElementById('step' + i + 'Label');
        if (i < step) {
            dot.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-done';
            dot.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            if (label) { label.className = 'text-xs font-bold text-green-600'; }
        } else if (i === step) {
            dot.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-active';
            dot.textContent = i;
            if (label) { label.className = 'text-xs font-bold text-gray-700'; }
        } else {
            dot.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold step-pending';
            dot.textContent = i;
            if (label) { label.className = 'text-xs font-bold text-gray-400'; }
        }
    }
}

function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 5000);
}
</script>

</body>
</html>
