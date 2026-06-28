<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Domains — {{ strtoupper($tenantId) }} Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-premium {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226,232,240,0.8);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04);
        }
        .step-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 12px; flex-shrink: 0; }
        .dns-record { font-family: monospace; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 11px; }
        .status-pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .copy-btn { transition: all 0.15s; }
        .copy-btn:hover { background: #6366f1; color: white; }
    </style>
</head>
<body class="min-h-screen antialiased pb-20">

    <!-- Top Nav -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-sky-500 to-indigo-500 text-white p-2.5 rounded-xl shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-sky-400 text-[10px] font-bold block uppercase tracking-wider">Custom Domains</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ $storeUrl }}" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition flex items-center gap-1">
                        View Storefront
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-10 px-6">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Custom Domains</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Use your own domain name (e.g. <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-bold">mybrand.com</code>) for your storefront</p>
            </div>
            <button onclick="refreshAllDns()" id="refreshBtn" class="bg-white border border-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs hover:bg-slate-50 transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Check DNS Status</span>
            </button>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 font-bold text-sm">
                <span class="text-lg">✓</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 font-bold text-sm">
                <span class="text-lg">⚠</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- How It Works Section -->
        <div class="mb-8 card-premium rounded-3xl p-6 md:p-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-lg text-slate-900">How Custom Domains Work</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">3 simple steps</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-start gap-3 bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl">
                    <div class="step-circle bg-indigo-500 text-white">1</div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Add Domain</h4>
                        <p class="text-xs text-slate-600 mt-1">Enter your custom domain name in the form below</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-violet-50/50 border border-violet-100 p-4 rounded-2xl">
                    <div class="step-circle bg-violet-500 text-white">2</div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Configure DNS</h4>
                        <p class="text-xs text-slate-600 mt-1">Add the DNS records at your domain registrar (GoDaddy, Namecheap, etc.)</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 bg-emerald-50/50 border border-emerald-100 p-4 rounded-2xl">
                    <div class="step-circle bg-emerald-500 text-white">3</div>
                    <div>
                        <h4 class="font-black text-sm text-slate-900">Go Live</h4>
                        <p class="text-xs text-slate-600 mt-1">DNS propagates, SSL auto-provisions, your store is live on your domain</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Domain Management -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Add Domain Form -->
                <div class="card-premium rounded-3xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="bg-sky-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900">Add Custom Domain</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Step 1 of 3</p>
                        </div>
                    </div>

                    <form action="/shop/domains" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Your Domain Name</label>
                            <input type="text" name="domain" placeholder="e.g. mybrand.com or store.mybrand.com" required
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition">
                            <p class="text-[11px] text-slate-400 mt-2 leading-relaxed">
                                Enter your root domain (<code class="bg-slate-50 px-1 rounded">mybrand.com</code>) or subdomain (<code class="bg-slate-50 px-1 rounded">store.mybrand.com</code>). Do not include <code>http://</code> or <code>https://</code>.
                            </p>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-950 text-white font-black text-sm py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            Link Domain
                        </button>
                    </form>
                </div>

                <!-- Active Domains Table -->
                <div class="card-premium rounded-3xl p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="bg-emerald-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-lg text-slate-900">Active Domains</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Your linked domains</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-400">{{ $domains->count() }} active</span>
                    </div>

                    @if($domains->count() === 0)
                        <div class="text-center py-12 text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            <p class="font-bold text-sm">No domains added yet</p>
                            <p class="text-xs mt-1">Add your first custom domain above</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($domains as $domain)
                                @php
                                    $isDefault = ($domain->domain === $defaultSubdomain);
                                    $status = $domainStatuses[$domain->domain] ?? 'resolving';
                                @endphp
                                <div class="border border-slate-200 rounded-2xl p-4 hover:border-indigo-200 transition" id="domain-{{ md5($domain->domain) }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <!-- Status Dot -->
                                            @if($status === 'connected')
                                                <div class="w-3 h-3 bg-emerald-500 rounded-full status-pulse"></div>
                                            @elseif($status === 'resolving')
                                                <div class="w-3 h-3 bg-amber-500 rounded-full animate-bounce"></div>
                                            @elseif($status === 'mismatch')
                                                <div class="w-3 h-3 bg-rose-500 rounded-full"></div>
                                            @else
                                                <div class="w-3 h-3 bg-slate-300 rounded-full"></div>
                                            @endif

                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-black text-sm text-slate-900" style="font-family: monospace;">{{ $domain->domain }}</span>
                                                    @if($isDefault)
                                                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-wider">System</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($status === 'connected')
                                                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">✓ Active — SSL Ready</span>
                                                    @elseif($status === 'resolving')
                                                        <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider status-pulse">⏳ DNS Propagating...</span>
                                                    @elseif($status === 'mismatch')
                                                        <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">✕ DNS Not Pointing Here</span>
                                                    @else
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unknown</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <!-- Refresh DNS Button -->
                                            <button onclick="checkDns('{{ $domain->domain }}')" class="text-slate-400 hover:text-indigo-600 p-2 rounded-lg hover:bg-indigo-50 transition" title="Check DNS">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>

                                            <!-- View Instructions Button -->
                                            @if(!$isDefault)
                                                <button onclick="showInstructions('{{ $domain->domain }}')" class="text-slate-400 hover:text-violet-600 p-2 rounded-lg hover:bg-violet-50 transition" title="DNS Instructions">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            @endif

                                            <!-- Delete Button -->
                                            @if(!$isDefault)
                                                <form action="/shop/domains/{{ $domain->id }}/delete" method="POST" onsubmit="return confirm('Delete this domain? The storefront will no longer be accessible from this URL.')">
                                                    @csrf
                                                    <button type="submit" class="text-slate-400 hover:text-rose-600 p-2 rounded-lg hover:bg-rose-50 transition" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: DNS Instructions Panel -->
            <div class="lg:col-span-5 space-y-6" id="instructions-panel">

                <!-- Default Instructions -->
                <div class="card-premium rounded-3xl p-6 md:p-8 border-violet-200/50 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-24 h-[3px] bg-violet-500"></div>

                    <div class="flex items-center gap-2 mb-6">
                        <div class="bg-violet-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900">DNS Setup Guide</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Follow these steps</p>
                        </div>
                    </div>

                    <!-- Step 1: Render Dashboard -->
                    <div class="mb-4 bg-rose-50/60 border border-rose-100/60 p-4 rounded-2xl">
                        <p class="text-xs text-rose-900 font-bold leading-relaxed mb-2">
                            <span class="bg-rose-500 text-white text-[9px] px-1.5 py-0.5 rounded font-black mr-1">STEP 1</span>
                            Add Domain in Render Dashboard
                        </p>
                        <ol class="text-[11px] text-rose-800 font-medium leading-relaxed ml-4 list-decimal space-y-1.5">
                            <li>Go to <strong>Render Dashboard</strong> → your service → <strong>Settings</strong></li>
                            <li>Scroll to <strong>Custom Domains</strong> → click <strong>Add Custom Domain</strong></li>
                            <li>Enter your domain name</li>
                            <li>Click <strong>Save</strong></li>
                            <li>Render will show you the DNS records — <strong>copy them exactly</strong></li>
                        </ol>
                    </div>

                    <!-- Step 2: DNS Records -->
                    <div class="mb-4 bg-violet-50/60 border border-violet-100/60 p-4 rounded-2xl">
                        <p class="text-xs text-violet-900 font-bold leading-relaxed mb-2">
                            <span class="bg-violet-500 text-white text-[9px] px-1.5 py-0.5 rounded font-black mr-1">STEP 2</span>
                            Add DNS Records at Your Registrar
                        </p>
                        <p class="text-[11px] text-violet-800 font-medium leading-relaxed">
                            Log in to <strong>GoDaddy</strong>, <strong>Namecheap</strong>, <strong>Cloudflare</strong>, or your domain provider and add these records:
                        </p>
                    </div>

                    <!-- DNS Records to Copy -->
                    <div class="space-y-3 mb-4">
                        <!-- CNAME Record -->
                        <div class="bg-white border border-slate-200 p-4 rounded-2xl space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="bg-slate-100 text-slate-700 text-[9px] px-2 py-0.5 rounded font-black">CNAME RECORD</span>
                                <span class="text-[10px] text-slate-400 font-bold">For www subdomain</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs font-bold text-slate-500">
                                <div>
                                    Host
                                    <div class="flex items-center mt-1 bg-slate-50 border rounded p-1.5">
                                        <code class="text-slate-800 font-black text-xs flex-grow text-center" style="font-family: monospace;">www</code>
                                        <button onclick="copyValue(this, 'www')" class="copy-btn text-slate-400 hover:text-white text-[9px] font-bold px-2 py-0.5 rounded border-l transition">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    Value
                                    <div class="flex items-center mt-1 bg-slate-50 border rounded p-1.5">
                                        <code class="text-indigo-600 font-black text-[10px] flex-grow text-center overflow-x-auto whitespace-nowrap" style="font-family: monospace;">{{ $platformDomain }}</code>
                                        <button onclick="copyValue(this, '{{ $platformDomain }}')" class="copy-btn text-slate-400 hover:text-white text-[9px] font-bold px-2 py-0.5 rounded border-l transition">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- A Record (if IP available) -->
                        @if(!empty($platformIp))
                        <div class="bg-white border border-slate-200 p-4 rounded-2xl space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="bg-slate-100 text-slate-700 text-[9px] px-2 py-0.5 rounded font-black">A RECORD</span>
                                <span class="text-[10px] text-slate-400 font-bold">For root domain</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs font-bold text-slate-500">
                                <div>
                                    Host
                                    <div class="flex items-center mt-1 bg-slate-50 border rounded p-1.5">
                                        <code class="text-slate-800 font-black text-xs flex-grow text-center" style="font-family: monospace;">@</code>
                                        <button onclick="copyValue(this, '@')" class="copy-btn text-slate-400 hover:text-white text-[9px] font-bold px-2 py-0.5 rounded border-l transition">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    Value
                                    <div class="flex items-center mt-1 bg-slate-50 border rounded p-1.5">
                                        <code class="text-indigo-600 font-black text-xs flex-grow text-center" style="font-family: monospace;">{{ $platformIp }}</code>
                                        <button onclick="copyValue(this, '{{ $platformIp }}')" class="copy-btn text-slate-400 hover:text-white text-[9px] font-bold px-2 py-0.5 rounded border-l transition">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Step 3: Wait -->
                    <div class="bg-emerald-50/60 border border-emerald-100/60 p-4 rounded-2xl mb-4">
                        <p class="text-xs text-emerald-900 font-bold leading-relaxed mb-1">
                            <span class="bg-emerald-500 text-white text-[9px] px-1.5 py-0.5 rounded font-black mr-1">STEP 3</span>
                            Wait for DNS Propagation
                        </p>
                        <p class="text-[11px] text-emerald-800 font-medium leading-relaxed">
                            DNS changes take <strong>1–24 hours</strong> to propagate worldwide. Once DNS is active, Render automatically provisions an <strong>SSL certificate</strong> (HTTPS) for your domain — no action needed from you.
                        </p>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex gap-2">
                        <span class="text-sm shrink-0">💡</span>
                        <p class="text-[10px] text-amber-800 font-semibold leading-relaxed">
                            <strong>Pro tip:</strong> After adding DNS records, click <strong>"Check DNS Status"</strong> above to see if your domain is connected. The status will change from <span class="text-amber-600">Resolving</span> to <span class="text-emerald-600">Active</span> once propagation is complete.
                        </p>
                    </div>
                </div>

                <!-- SSL Info Card -->
                <div class="card-premium rounded-3xl p-6 border-emerald-200/50 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-24 h-[3px] bg-emerald-500"></div>
                    <div class="flex items-center gap-3">
                        <div class="bg-emerald-100 text-emerald-700 w-10 h-10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-slate-900">SSL is Automatic</h4>
                            <p class="text-[11px] text-slate-500">Render provisions a free SSL certificate (HTTPS) for your custom domain automatically once DNS is configured.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Instructions Modal (for individual domains) -->
    <div id="instructionsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 md:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-lg text-slate-900" id="modal-title">DNS Instructions</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="modal-content" class="space-y-4"></div>
        </div>
    </div>

    <script>
        function copyValue(btn, value) {
            navigator.clipboard.writeText(value).then(() => {
                const orig = btn.innerText;
                btn.innerText = 'Copied!';
                btn.classList.add('bg-emerald-500', 'text-white', 'border-emerald-500');
                setTimeout(() => {
                    btn.innerText = orig;
                    btn.classList.remove('bg-emerald-500', 'text-white', 'border-emerald-500');
                }, 1500);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = value;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            });
        }

        async function checkDns(domain) {
            const el = document.getElementById('domain-' + md5(domain));
            if (!el) return;

            try {
                const res = await fetch('/shop/domains/check-dns', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ domain }),
                });
                const data = await res.json();

                // Update status badge
                const statusBadge = el.querySelector('.text-\\[10px\\]');
                if (data.status === 'connected') {
                    statusBadge.className = 'text-[10px] font-bold text-emerald-600 uppercase tracking-wider';
                    statusBadge.textContent = '✓ Active — SSL Ready';
                    el.querySelector('.w-3.h-3').className = 'w-3 h-3 bg-emerald-500 rounded-full status-pulse';
                } else if (data.status === 'resolving') {
                    statusBadge.className = 'text-[10px] font-bold text-amber-600 uppercase tracking-wider status-pulse';
                    statusBadge.textContent = '⏳ DNS Propagating...';
                    el.querySelector('.w-3.h-3').className = 'w-3 h-3 bg-amber-500 rounded-full animate-bounce';
                } else {
                    statusBadge.className = 'text-[10px] font-bold text-rose-600 uppercase tracking-wider';
                    statusBadge.textContent = '✕ DNS Not Pointing Here';
                    el.querySelector('.w-3.h-3').className = 'w-3 h-3 bg-rose-500 rounded-full';
                }
            } catch (err) {
                console.error('DNS check failed:', err);
            }
        }

        async function refreshAllDns() {
            const btn = document.getElementById('refreshBtn');
            btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9"/></svg> Checking...';
            btn.disabled = true;

            try {
                await fetch('/shop/domains/refresh', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                window.location.reload();
            } catch (err) {
                console.error('Refresh failed:', err);
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9"/></svg> Check DNS Status';
                btn.disabled = false;
            }
        }

        async function showInstructions(domain) {
            const modal = document.getElementById('instructionsModal');
            const content = document.getElementById('modal-content');
            const title = document.getElementById('modal-title');

            title.textContent = 'DNS Setup for ' + domain;

            try {
                const res = await fetch('/shop/domains/instructions?domain=' + encodeURIComponent(domain), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                let html = '<div class="bg-rose-50/60 border border-rose-100/60 p-4 rounded-2xl mb-4">';
                html += '<p class="text-xs text-rose-900 font-bold mb-2"><span class="bg-rose-500 text-white text-[9px] px-1.5 py-0.5 rounded font-black mr-1">STEP 1</span> Add in Render Dashboard</p>';
                html += '<ol class="text-[11px] text-rose-800 font-medium ml-4 list-decimal space-y-1">';
                data.render_steps.steps.forEach(s => { html += '<li>' + s + '</li>'; });
                html += '</ol></div>';

                html += '<div class="bg-violet-50/60 border border-violet-100/60 p-4 rounded-2xl mb-4">';
                html += '<p class="text-xs text-violet-900 font-bold mb-2"><span class="bg-violet-500 text-white text-[9px] px-1.5 py-0.5 rounded font-black mr-1">STEP 2</span> Add DNS Records</p></div>';

                data.records.forEach(r => {
                    html += '<div class="bg-white border border-slate-200 p-3 rounded-xl space-y-2 mb-3">';
                    html += '<span class="bg-slate-100 text-slate-700 text-[9px] px-2 py-0.5 rounded font-black">' + r.type + ' RECORD</span>';
                    html += '<div class="grid grid-cols-2 gap-2 text-xs font-bold text-slate-500">';
                    html += '<div>Host<div class="bg-slate-50 border rounded p-1.5 mt-1 text-center" style="font-family:monospace;">' + r.host + '</div></div>';
                    html += '<div>Value<div class="bg-slate-50 border rounded p-1.5 mt-1 text-center text-indigo-600" style="font-family:monospace;">' + r.value + '</div></div>';
                    html += '</div></div>';
                });

                content.innerHTML = html;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (err) {
                console.error('Failed to load instructions:', err);
            }
        }

        function closeModal() {
            const modal = document.getElementById('instructionsModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function md5(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                const char = str.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash;
            }
            return Math.abs(hash).toString(16);
        }

        document.getElementById('instructionsModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>

</body>
</html>
