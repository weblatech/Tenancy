<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp CRM - Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        }
        .input-premium {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .input-premium:focus {
            border-color: #25D366;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
            outline: none;
        }
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider { background-color: #25D366; }
        input:checked + .toggle-slider:before { transform: translateX(20px); }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased pb-20">

    <!-- Top Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-green-500 to-emerald-500 text-white p-2.5 rounded-xl shadow-lg shadow-green-500/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-green-400 text-[10px] font-bold block uppercase tracking-wider">WhatsApp CRM</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/shop/whatsapp-chat" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Chat</span>
                    </a>
                    <a href="/shop/whatsapp-logs" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Logs</span>
                    </a>
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 px-6">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">WhatsApp CRM Settings</h1>
            <p class="text-slate-500 font-medium text-sm mt-1">Configure automated order notifications for your store</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 font-bold text-sm shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 font-bold text-sm shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <a href="/shop/whatsapp-chat" class="card-premium rounded-2xl p-5 flex items-center gap-4 hover:border-green-300 transition group">
                <div class="bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">Chat with Customers</h3>
                    <p class="text-[10px] text-slate-400 font-medium">View conversations & reply</p>
                </div>
            </a>
            <a href="/shop/whatsapp-logs" class="card-premium rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 transition group">
                <div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">Message Logs</h3>
                    <p class="text-[10px] text-slate-400 font-medium">All sent & received messages</p>
                </div>
            </a>
            <div class="card-premium rounded-2xl p-5 flex items-center gap-4 bg-slate-50">
                <div class="bg-slate-400 text-white w-12 h-12 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">API Status</h3>
                    <p class="text-[10px] {{ $isConfigured ? 'text-green-600' : 'text-rose-500' }} font-bold">{{ $isConfigured ? 'Connected' : 'Not Configured' }}</p>
                </div>
            </div>
        </div>

        <form action="/shop/whatsapp-crm" method="POST">
            @csrf

            <!-- Enable/Disable CRM -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-green-500"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900">WhatsApp Automation</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Enable automated order notifications</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="whatsapp_crm_active" value="1" {{ $settings->whatsapp_crm_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                @if(!$isConfigured)
                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs text-amber-800 font-bold">API Not Configured</p>
                        <p class="text-[10px] text-amber-700 mt-1">Ask your platform administrator to configure the WhatsApp Business API provider first.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- WhatsApp Number Configuration -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-blue-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">WhatsApp Number Configuration</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Set up your store's WhatsApp for sending messages</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">WhatsApp Phone Number ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_phone_number_id" value="{{ $settings->whatsapp_phone_number_id ?? '' }}" placeholder="e.g. 1234567890" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">Your WhatsApp Business Phone Number ID from Meta Cloud API</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Webhook Verify Token</label>
                        <input type="text" name="whatsapp_verify_token" value="{{ $settings->whatsapp_verify_token ?? '' }}" placeholder="e.g. my_store_verify_123" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">Used for webhook verification (must match your Meta app config)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Store WhatsApp Number (Display)</label>
                        <input type="text" name="footer_whatsapp" value="{{ $settings->footer_whatsapp ?? '' }}" placeholder="e.g. 03001234567" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">The WhatsApp number shown on your storefront</p>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Web Device Linking -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden" id="waWebCard">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-[#25d366]"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-[#25d366] text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">Link WhatsApp Device</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Connect your WhatsApp to send & receive messages</p>
                    </div>
                </div>

                <!-- Status indicator -->
                <div id="waWebStatus" class="mb-6">
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div id="waWebStatusDot" class="w-3 h-3 rounded-full bg-slate-300 shrink-0"></div>
                        <div>
                            <p id="waWebStatusText" class="text-sm font-bold text-slate-500">Checking server...</p>
                            <p id="waWebStatusDetail" class="text-[10px] text-slate-400">Connecting to WhatsApp Web server</p>
                        </div>
                    </div>
                </div>

                <!-- QR Code Area -->
                <div id="waWebQR" class="hidden mb-6">
                    <div class="flex flex-col items-center gap-4 p-6 bg-white border-2 border-dashed border-green-300 rounded-3xl">
                        <div class="bg-white p-4 rounded-2xl shadow-lg">
                            <img id="waQRImage" src="" alt="WhatsApp QR Code" class="w-56 h-56">
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-black text-slate-900">Scan QR Code with WhatsApp</p>
                            <p class="text-[10px] text-slate-500 mt-1">Open WhatsApp on your phone → Menu → Linked Devices → Link a Device</p>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] text-slate-400">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Waiting for scan...
                        </div>
                    </div>
                </div>

                <!-- Connected State -->
                <div id="waWebConnected" class="hidden mb-6">
                    <div class="flex items-center gap-4 p-5 bg-green-50 border border-green-200 rounded-2xl">
                        <div class="w-14 h-14 rounded-full bg-green-600 text-white flex items-center justify-center shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-black text-green-800">WhatsApp Connected</p>
                            <p id="waWebPhone" class="text-xs font-bold text-green-600 mt-0.5"></p>
                            <p class="text-[10px] text-green-500 mt-0.5">Messages will be sent and received automatically</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button id="waWebLinkBtn" onclick="startWaWeb()" class="bg-[#25d366] hover:bg-[#128c7e] text-white font-black text-xs px-6 py-3 rounded-xl transition shadow-md shadow-green-500/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                        Link Device
                    </button>
                    <button id="waWebDisconnectBtn" onclick="logoutWaWeb()" class="hidden bg-rose-500 hover:bg-rose-600 text-white font-black text-xs px-6 py-3 rounded-xl transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Disconnect
                    </button>
                    <span id="waWebLoading" class="hidden text-[10px] text-slate-400 font-bold animate-pulse">Connecting...</span>
                </div>

                <!-- Server Offline Warning -->
                <div id="waWebServerOffline" class="hidden mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs text-amber-800 font-bold">Server Not Running</p>
                        <p class="text-[10px] text-amber-700 mt-1">Start the WhatsApp Web server from command line: <code class="bg-amber-100 px-1.5 py-0.5 rounded font-mono">cd whatsapp-server && node server.js</code></p>
                    </div>
                </div>
            </div>

            <script>
                let waPollInterval = null;

                function updateWaStatus() {
                    fetch('/whatsapp-web/status')
                        .then(r => r.json())
                        .then(data => {
                            const status = data.status || 'disconnected';
                            const dot = document.getElementById('waWebStatusDot');
                            const text = document.getElementById('waWebStatusText');
                            const detail = document.getElementById('waWebStatusDetail');
                            const qrSection = document.getElementById('waWebQR');
                            const connected = document.getElementById('waWebConnected');
                            const linkBtn = document.getElementById('waWebLinkBtn');
                            const disconnectBtn = document.getElementById('waWebDisconnectBtn');
                            const loading = document.getElementById('waWebLoading');
                            const offline = document.getElementById('waWebServerOffline');
                            const phone = document.getElementById('waWebPhone');

                            // Reset
                            qrSection?.classList.add('hidden');
                            connected?.classList.add('hidden');
                            linkBtn?.classList.remove('hidden');
                            disconnectBtn?.classList.add('hidden');
                            loading?.classList.add('hidden');
                            offline?.classList.add('hidden');

                            if (status === 'server_offline') {
                                dot.className = 'w-3 h-3 rounded-full bg-amber-400 shrink-0';
                                text.textContent = 'Server Offline';
                                text.className = 'text-sm font-bold text-amber-600';
                                detail.textContent = 'WhatsApp Web server is not running';
                                offline?.classList.remove('hidden');
                            } else if (status === 'qr') {
                                dot.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse shrink-0';
                                text.textContent = 'Scan QR Code';
                                text.className = 'text-sm font-bold text-green-700';
                                detail.textContent = 'Scan with your phone to link WhatsApp';
                                qrSection?.classList.remove('hidden');
                                linkBtn?.classList.add('hidden');
                                qrSection?.classList.remove('hidden');
                                // Load QR image
                                fetch('/whatsapp-web/qr')
                                    .then(r => r.json())
                                    .then(qrData => {
                                        const img = document.getElementById('waQRImage');
                                        if (qrData.qr) img.src = qrData.qr;
                                    });
                            } else if (status === 'connected') {
                                dot.className = 'w-3 h-3 rounded-full bg-green-500 shrink-0';
                                text.textContent = 'Connected';
                                text.className = 'text-sm font-bold text-green-700';
                                detail.textContent = 'WhatsApp is active and ready';
                                connected?.classList.remove('hidden');
                                linkBtn?.classList.add('hidden');
                                disconnectBtn?.classList.remove('hidden');
                                if (data.phone) {
                                    phone.textContent = '+' + data.phone;
                                }
                            } else if (status === 'connecting' || status === 'reconnecting') {
                                dot.className = 'w-3 h-3 rounded-full bg-yellow-500 animate-pulse shrink-0';
                                text.textContent = status === 'reconnecting' ? 'Reconnecting...' : 'Connecting...';
                                text.className = 'text-sm font-bold text-yellow-600';
                                detail.textContent = 'Please wait...';
                                loading?.classList.remove('hidden');
                                loading.textContent = status === 'reconnecting' ? 'Reconnecting...' : 'Connecting...';
                            } else {
                                dot.className = 'w-3 h-3 rounded-full bg-slate-300 shrink-0';
                                text.textContent = 'Disconnected';
                                text.className = 'text-sm font-bold text-slate-500';
                                detail.textContent = 'Click "Link Device" to connect WhatsApp';
                            }

                            // If QR, keep polling for status changes (scan completed)
                            if (status === 'qr' || status === 'connecting') {
                                if (!waPollInterval) {
                                    waPollInterval = setInterval(checkWaStatusChange, 2000);
                                }
                            } else {
                                if (waPollInterval) {
                                    clearInterval(waPollInterval);
                                    waPollInterval = null;
                                }
                            }
                        })
                        .catch(() => {
                            const dot = document.getElementById('waWebStatusDot');
                            if (dot) {
                                dot.className = 'w-3 h-3 rounded-full bg-amber-400 shrink-0';
                            }
                        });
                }

                function checkWaStatusChange() {
                    fetch('/whatsapp-web/status')
                        .then(r => r.json())
                        .then(data => {
                            const status = data.status || 'disconnected';
                            if (status === 'connected' || status === 'disconnected' || status === 'server_offline') {
                                if (waPollInterval) {
                                    clearInterval(waPollInterval);
                                    waPollInterval = null;
                                }
                            }
                            updateWaStatus();
                        });
                }

                function startWaWeb() {
                    const btn = document.getElementById('waWebLinkBtn');
                    const loading = document.getElementById('waWebLoading');
                    btn.disabled = true;
                    btn.classList.add('opacity-50');
                    loading?.classList.remove('hidden');
                    loading.textContent = 'Starting...';

                    fetch('/whatsapp-web/start', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json',
                        }
                    })
                        .then(r => r.json())
                        .then(data => {
                            setTimeout(updateWaStatus, 3000);
                        })
                        .catch(() => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50');
                            loading?.classList.add('hidden');
                        });
                }

                function logoutWaWeb() {
                    if (!confirm('Disconnect WhatsApp? You will need to scan QR code again.')) return;
                    const btn = document.getElementById('waWebDisconnectBtn');
                    btn.disabled = true;
                    btn.classList.add('opacity-50');

                    fetch('/whatsapp-web/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json',
                        }
                    })
                        .then(r => r.json())
                        .then(() => {
                            setTimeout(updateWaStatus, 1000);
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50');
                        });
                }

                // Initial status check
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(updateWaStatus, 500);
                });
            </script>

            <!-- Message Templates -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-emerald-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-emerald-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">Message Templates</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customize messages for each order status</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <!-- Order Pending -->
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-amber-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            New Order Pending (with Confirm/Cancel buttons)
                        </label>
                        <textarea name="whatsapp_msg_order_pending" rows="4" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_pending ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {'{customer_name}'}, {'{order_id}'}, {'{store_name}'}, {'{items}'}, {'{total}'}, {'{address}'}, {'{phone}'}, {'{payment_method}'}</p>
                    </div>

                    <!-- Order Confirmed -->
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-green-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Order Confirmed
                        </label>
                        <textarea name="whatsapp_msg_order_confirmed" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_confirmed ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {'{customer_name}'}, {'{order_id}'}, {'{store_name}'}, {'{total}'}</p>
                    </div>

                    <!-- Order Processing -->
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-blue-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Order Processing / Shipped
                        </label>
                        <textarea name="whatsapp_msg_order_processing" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_processing ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {'{customer_name}'}, {'{order_id}'}, {'{store_name}'}</p>
                    </div>

                    <!-- Order Completed -->
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-indigo-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                            Order Completed / Delivered
                        </label>
                        <textarea name="whatsapp_msg_order_completed" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_completed ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {'{customer_name}'}, {'{order_id}'}, {'{store_name}'}, {'{total}'}</p>
                    </div>

                    <!-- Order Cancelled -->
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-rose-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                            Order Cancelled
                        </label>
                        <textarea name="whatsapp_msg_order_cancelled" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_cancelled ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {'{customer_name}'}, {'{order_id}'}, {'{store_name}'}</p>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <p class="text-xs text-slate-600 font-semibold leading-relaxed">
                        <strong>Tip:</strong> Leave templates empty to use the system default messages. The "Order Pending" message automatically includes Confirm and Cancel buttons for the customer.
                    </p>
                </div>
            </div>

            <!-- Save Button -->
            <div class="sticky bottom-0 bg-white/80 backdrop-blur-xl border-t border-slate-200 -mx-6 px-6 py-4 rounded-b-3xl">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-medium">Changes are saved per-tenant</p>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-black text-sm px-8 py-3 rounded-xl transition duration-200 shadow-md shadow-green-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </div>
            </div>
        </form>

    </div>

</body>
</html>
