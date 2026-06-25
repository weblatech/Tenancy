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
        .fb-login-btn { background: #1877F2; color: white; font-weight: 700; border-radius: 12px; padding: 12px 24px; transition: all 0.2s; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; font-size: 14px; }
        .fb-login-btn:hover { background: #166FE5; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(24,119,242,0.3); }
        .fb-login-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
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
        <p class="text-gray-500 text-sm mt-1">Sign in with Facebook to link your WhatsApp Business account</p>
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

    <!-- Step 1: Connect with Facebook -->
    <div id="step1" class="glass rounded-3xl p-8 shadow-xl animate-slide-up" style="animation-delay: 0.2s">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-2xl mb-3">
                <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </div>
            <h2 class="text-lg font-black text-gray-900">Connect with Facebook</h2>
            <p class="text-gray-500 text-xs mt-1">Sign in to link your WhatsApp Business Account</p>
        </div>

        <!-- Info about the flow -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6">
            <p class="text-xs text-blue-800 font-bold mb-2">What happens next:</p>
            <ol class="text-[11px] text-blue-700 space-y-1.5 list-decimal list-inside">
                <li>Facebook will open a popup window</li>
                <li>Sign in and select your WhatsApp Business Account</li>
                <li>Choose the phone number you want to connect</li>
                <li>We'll securely save your credentials</li>
            </ol>
        </div>

        <!-- Facebook Login Button -->
        <div class="text-center">
            <button id="fbLoginBtn" onclick="connectWithFB()" class="fb-login-btn w-full justify-center text-base py-4">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span id="fbLoginText">Continue with Facebook</span>
            </button>

            <!-- Hidden FB SDK login button (for config_id flow) -->
            <div id="fbLoginBtnWrapper" style="display:none;">
                <fb:login-button
                    id="fbLoginButton"
                    config_id="{{ config('services.meta.config_id') }}"
                    onlogin="checkLoginState();"
                    size="large"
                    button_style="rounded"
                    width="100%">
                </fb:login-button>
            </div>
        </div>

        <!-- Loading state -->
        <div id="connectingLoader" class="hidden text-center py-8">
            <svg class="w-10 h-10 animate-spin text-blue-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <p class="text-sm font-bold text-gray-700" id="connectingText">Connecting to Facebook...</p>
            <p class="text-xs text-gray-400 mt-1">Please complete the login in the popup</p>
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

    <!-- Step 2: Select Phone Number (shown after FB token exchange) -->
    <div id="step1b" class="glass rounded-3xl p-8 shadow-xl hidden animate-slide-up">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-green-100 rounded-2xl mb-3">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <h2 class="text-lg font-black text-gray-900">Select Your Number</h2>
            <p class="text-gray-500 text-xs mt-1">Choose which phone number to connect</p>
        </div>

        <!-- WABA info -->
        <div id="wabaInfo" class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mb-6 hidden">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase">WhatsApp Business Account</p>
                    <p class="text-sm font-black text-gray-900" id="wabaName">---</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Connected</span>
            </div>
        </div>

        <!-- Phone numbers list -->
        <div id="phoneNumbersList" class="space-y-3"></div>

        <!-- Loading -->
        <div id="phoneNumbersLoader" class="text-center py-6">
            <svg class="w-6 h-6 animate-spin text-green-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <p class="text-xs text-gray-500 font-bold">Loading phone numbers...</p>
        </div>

        <!-- Back button -->
        <button onclick="goBackToStep1()" class="mt-6 w-full text-center text-xs text-gray-400 font-bold hover:text-gray-600 transition">
            &larr; Use a different account
        </button>
    </div>

    <!-- Step 3: Success -->
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

<!-- Facebook SDK -->
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '{{ config("services.meta.app_id") }}',
            cookie: true,
            xfbml: true,
            version: 'v18.0'
        });
        console.log('[FB SDK] Initialized');
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) { return; }
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// ============================================================
// Meta Embedded Signup Flow
// ============================================================

/**
 * Open the FB Login popup with Embedded Signup permissions.
 * Uses FB.login() with config_id for the Embedded Signup flow.
 */
function connectWithFB() {
    const appId = '{{ config("services.meta.app_id") }}';
    const configId = '{{ config("services.meta.config_id") }}';

    if (!appId) {
        showError('step1Error', 'Facebook App ID not configured. Ask your admin to set META_APP_ID.');
        return;
    }

    // Show loading state
    setConnecting(true, 'Opening Facebook login...');

    if (configId && typeof FB !== 'undefined') {
        // Use the Embedded Signup config_id flow (preferred)
        console.log('[FB] Using Embedded Signup config_id:', configId);
        FB.login(function(response) {
            handleFBResponse(response);
        }, {
            config_id: configId,
            response_type: 'code',
            override_default_response_type: true,
            extras: {
                setup: {
                    // Pre-fill business info if available
                }
            }
        });
    } else if (typeof FB !== 'undefined') {
        // Fallback: standard FB.login with WhatsApp permissions
        console.log('[FB] Using standard login flow');
        FB.login(function(response) {
            handleFBResponse(response);
        }, {
            scope: 'whatsapp_business_management,whatsapp_business_messaging',
            display: 'popup'
        });
    } else {
        // FB SDK not loaded yet
        showError('step1Error', 'Facebook SDK is loading. Please try again in a moment.');
        setConnecting(false);
    }
}

/**
 * Handle the FB login response.
 * If connected, extract the access token and send to the backend.
 */
function handleFBResponse(response) {
    console.log('[FB] Login response:', response);

    if (!response || response.status !== 'connected') {
        setConnecting(false);
        if (response && response.authResponse) {
            // User may have cancelled or denied permissions
            showError('step1Error', 'Facebook login was cancelled or permissions were denied. Please try again.');
        }
        return;
    }

    const accessToken = response.authResponse.accessToken;
    const userID = response.authResponse.userID;

    setConnecting(true, 'Connecting your WhatsApp account...');

    // Send to backend for token exchange and WABA discovery
    fetch('/shop/whatsapp/connect', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            access_token: accessToken,
            user_id: userID,
        }),
    })
    .then(resp => resp.json())
    .then(data => {
        console.log('[Backend] Connect response:', data);

        if (data.success) {
            const phoneNumbers = data.phone_numbers || [];

            if (phoneNumbers.length === 0) {
                // Connected but no phone numbers found
                showError('step1Error', 'Connected to Facebook but no phone numbers found in your WhatsApp Business Account. Please add a phone number in Meta Business Suite.');
                setConnecting(false);
                return;
            }

            if (phoneNumbers.length === 1) {
                // Only one phone number — auto-select it
                const pn = phoneNumbers[0];
                savePhoneSelection(pn.id, pn.display_phone_number, pn.verified_name);
            } else {
                // Show phone number selection
                showPhoneSelection(phoneNumbers, data.waba_name);
                setConnecting(false);
            }
        } else {
            showError('step1Error', data.error || 'Failed to connect. Please try again.');
            setConnecting(false);
        }
    })
    .catch(err => {
        console.error('[Backend] Error:', err);
        showError('step1Error', 'Network error: ' + err.message);
        setConnecting(false);
    });
}

/**
 * Check FB login state (called by the FB SDK's onlogin callback).
 */
function checkLoginState() {
    FB.getLoginStatus(function(response) {
        handleFBResponse(response);
    });
}

// ============================================================
// Phone Number Selection
// ============================================================

/**
 * Show the phone number selection step.
 */
function showPhoneSelection(phoneNumbers, wabaName) {
    // Update WABA info
    if (wabaName) {
        document.getElementById('wabaName').textContent = wabaName;
        document.getElementById('wabaInfo').classList.remove('hidden');
    }

    // Build phone number cards
    const list = document.getElementById('phoneNumbersList');
    list.innerHTML = phoneNumbers.map(pn => {
        const phone = pn.display_phone_number || 'Unknown';
        const name = pn.verified_name || 'Unnamed';
        const pnId = pn.id || '';
        const status = pn.status || 'unknown';
        const quality = pn.quality_rating || '';

        return `
            <div class="flex items-center p-4 bg-white border border-gray-200 rounded-2xl hover:border-green-300 hover:bg-green-50/50 transition cursor-pointer group" onclick="savePhoneSelection('${pnId}', '${phone}', '${name}')">
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

    document.getElementById('phoneNumbersLoader').classList.add('hidden');
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step1b').classList.remove('hidden');
}

/**
 * Save the selected phone number using the existing select-number endpoint.
 */
function savePhoneSelection(pnId, phone, name) {
    setConnecting(true, 'Saving your phone number...');

    fetch('/shop/whatsapp-register/select-number', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
        },
        body: JSON.stringify({ phone_number_id: pnId }),
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            document.getElementById('connectedPhone').textContent = data.phone_number || phone || 'N/A';
            document.getElementById('connectedPnId').textContent = data.phone_number_id || pnId;
            document.getElementById('step1b').classList.add('hidden');
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        } else {
            showError('step1Error', data.error || 'Failed to save phone number');
            setConnecting(false);
        }
    })
    .catch(err => {
        showError('step1Error', 'Network error: ' + err.message);
        setConnecting(false);
    });
}

// ============================================================
// Manual Phone Number Entry (existing flow)
// ============================================================

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

// ============================================================
// UI Helpers
// ============================================================

function goBackToStep1() {
    document.getElementById('step1b').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    setConnecting(false);
}

function setConnecting(show, text) {
    const btn = document.getElementById('fbLoginBtn');
    const loader = document.getElementById('connectingLoader');
    const connectingText = document.getElementById('connectingText');

    if (show) {
        btn.style.display = 'none';
        loader.classList.remove('hidden');
        if (text) connectingText.textContent = text;
    } else {
        btn.style.display = 'inline-flex';
        btn.disabled = false;
        document.getElementById('fbLoginText').textContent = 'Continue with Facebook';
        loader.classList.add('hidden');
    }
}

function showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 10000);
}
</script>

</body>
</html>
