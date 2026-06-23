<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌐 Custom Domains Mapping — Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
            background-size: cover;
            background-attachment: fixed;
        }
        .dotted-overlay {
            background-image: radial-gradient(#cbd5e1 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .input-premium {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }
        .input-premium:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased pb-20 relative overflow-x-hidden">

    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>

    <!-- Top Premium Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-sky-500 to-indigo-500 text-white p-2.5 rounded-xl shadow-lg shadow-sky-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-sky-400 text-[10px] font-bold block uppercase tracking-wider">Domains Portal</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ $storeUrl }}" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>View Storefront</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Workspace -->
    <div class="max-w-6xl mx-auto mt-10 px-6 relative z-10">
        
        <!-- Header Info -->
        <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Custom Domains</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Map your own custom domain name to your storefront</p>
            </div>
            
            <!-- Quick refresh button -->
            <button onclick="window.location.reload()" class="bg-white border border-slate-200 text-slate-700 font-bold px-4 py-2.5 rounded-xl text-xs hover:bg-slate-50 hover:border-slate-300 transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Refresh Status</span>
            </button>
        </div>

        <!-- Success & Error Toast Alerts -->
        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 font-bold text-sm shadow-sm">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 font-bold text-sm shadow-sm">
                <span>⚠️</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Domain Management Form & Table (Col: 7) -->
            <div class="lg:col-span-7 space-y-8">
                
                <!-- Card: Add Domain -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8">
                    <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                    <div class="flex items-center gap-2 mb-6">
                        <div class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900 leading-tight">Link Custom Domain</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Add a new domain</p>
                        </div>
                    </div>

                    <form action="/shop/domains" method="POST" class="space-y-4 text-left">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Domain Name</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="text" name="domain" placeholder="e.g. myboutique.com" required class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium mt-1.5 leading-relaxed">Enter your root domain (e.g. <code>mybrand.com</code>) or subdomain (e.g. <code>store.mybrand.com</code>) without <code>http://</code>.</p>
                        </div>

                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-950 text-white font-black text-xs py-3.5 rounded-xl transition duration-200 shadow-md shadow-slate-950/10 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span>Link Domain Name</span>
                        </button>
                    </form>
                </div>

                <!-- Card: Active Mappings -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 space-y-6">
                    <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="bg-sky-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-sky-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-lg text-slate-900 leading-tight">Active Mappings</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Linked domains</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-400">{{ $domains->count() }} active</span>
                    </div>

                    <!-- Domains Table -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-150 bg-white shadow-sm">
                        <table class="min-w-full leading-normal text-left text-xs font-semibold text-slate-600">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-150 text-slate-400 font-extrabold uppercase tracking-wider">
                                    <th class="px-5 py-4">Domain Alias</th>
                                    <th class="px-5 py-4">Type</th>
                                    <th class="px-5 py-4 text-center">DNS Status</th>
                                    <th class="px-5 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                @php
                                    // Helper function for DNS check
                                    if (!function_exists('checkDomainDns')) {
                                        function checkDomainDns($domainName, $platformIp) {
                                            // Localhost domains are always connected
                                            if (str_ends_with($domainName, 'localhost') || $domainName === '127.0.0.1') {
                                                return 'connected';
                                            }
                                            if (!filter_var(gethostbyname($domainName), FILTER_VALIDATE_IP)) {
                                                return 'pending';
                                            }
                                            $ip = gethostbyname($domainName);
                                            if ($ip === $domainName) {
                                                return 'pending'; // DNS not resolved yet
                                            }
                                            if ($ip === $platformIp || $ip === '127.0.0.1') {
                                                return 'connected';
                                            }
                                            return 'mismatch';
                                        }
                                    }
                                @endphp

                                @foreach($domains as $index => $domain)
                                    @php
                                        $isSystemDefault = ($domain->domain === $defaultSubdomain);
                                        $status = checkDomainDns($domain->domain, $platformIp);
                                    @endphp
                                    <tr class="hover:bg-slate-50/40 transition">
                                        <!-- DOMAIN NAME -->
                                        <td class="px-5 py-4">
                                            <span class="font-black text-slate-900 block select-all" style="font-family: monospace;">{{ $domain->domain }}</span>
                                        </td>

                                        <!-- TYPE BADGE -->
                                        <td class="px-5 py-4">
                                            @if($isSystemDefault)
                                                <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-wider">System</span>
                                            @else
                                                <span class="bg-sky-50 text-sky-600 border border-sky-100 text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-wider">Custom</span>
                                            @endif
                                        </td>

                                        <!-- DNS STATUS BADGE -->
                                        <td class="px-5 py-4 text-center">
                                            @if($status === 'connected')
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Active
                                                </span>
                                            @elseif($status === 'mismatch')
                                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 border border-rose-100 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider">
                                                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span> IP Mismatch
                                                </span>
                                            @elseif($status === 'invalid')
                                                <span class="inline-flex items-center gap-1 bg-slate-50 text-slate-500 border border-slate-100 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider">
                                                    ✕ Invalid
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-100 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider">
                                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce"></span> Resolving
                                                </span>
                                            @endif
                                        </td>

                                        <!-- ACTION -->
                                        <td class="px-5 py-4 text-right">
                                            @if($isSystemDefault)
                                                <span class="text-slate-300 text-xs font-medium cursor-not-allowed select-none">Locked</span>
                                            @else
                                                <form action="/shop/domains/{{ $domain->id }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this custom domain mapping?')" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 hover:underline text-xs font-black transition">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Side: DNS Settings Card (Col: 5) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Card: DNS configuration instructions -->
                <div class="card-premium rounded-3xl p-6 md:p-8 space-y-6 border-indigo-200/50 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                    <div class="absolute -top-10 -right-10 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl pointer-events-none"></div>

                    <div class="flex items-center gap-2">
                        <div class="bg-violet-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-violet-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900 leading-tight">DNS Setup Instructions</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">How to connect your domain</p>
                        </div>
                    </div>

                    <!-- Instructions description -->
                    <div class="bg-violet-50/60 border border-violet-100/60 p-4 rounded-2xl">
                        <p class="text-xs text-violet-900 font-semibold leading-relaxed">
                            To connect your custom domain (e.g. <strong>mydomain.com</strong>) to your storefront, log in to your domain provider (GoDaddy, Namecheap, Cloudflare, etc.) hosting panel and add the records below:
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Record 1: A Record -->
                        <div class="bg-white border border-slate-200 p-4 rounded-2xl space-y-3 shadow-inner">
                            <div class="flex justify-between items-center">
                                <span class="bg-slate-100 text-slate-700 text-[9px] px-2 py-0.5 rounded font-black">A RECORD</span>
                                <span class="text-[10px] text-slate-400 font-bold">Root Mapping</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs font-bold text-slate-500">
                                <div>Host: <code class="text-slate-800 font-black block mt-0.5 text-xs bg-slate-50 p-1 rounded text-center">@</code></div>
                                <div class="col-span-2 relative">
                                    Value (IP): 
                                    <div class="flex items-center mt-0.5 bg-slate-50 border rounded p-1">
                                        <code id="ip-val" class="text-indigo-600 font-black text-xs select-all shrink-0 flex-grow text-center" style="font-family: monospace;">{{ $platformIp }}</code>
                                        <button onclick="copyText('ip-val', this)" class="text-slate-400 hover:text-indigo-600 font-bold text-[9px] ml-1 shrink-0 px-1 border-l transition-colors duration-150">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Record 2: CNAME Record -->
                        <div class="bg-white border border-slate-200 p-4 rounded-2xl space-y-3 shadow-inner">
                            <div class="flex justify-between items-center">
                                <span class="bg-slate-100 text-slate-700 text-[9px] px-2 py-0.5 rounded font-black">CNAME RECORD</span>
                                <span class="text-[10px] text-slate-400 font-bold">Subdomain Mapping</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs font-bold text-slate-500">
                                <div>Host: <code class="text-slate-800 font-black block mt-0.5 text-xs bg-slate-50 p-1 rounded text-center">www</code></div>
                                <div class="col-span-2 relative">
                                    Value: 
                                    <div class="flex items-center mt-0.5 bg-slate-50 border rounded p-1">
                                        <code id="sub-val" class="text-indigo-600 font-black text-[10px] select-all shrink-0 flex-grow text-center overflow-x-auto whitespace-nowrap scrollbar-thin" style="font-family: monospace;">{{ $defaultSubdomain }}</code>
                                        <button onclick="copyText('sub-val', this)" class="text-slate-400 hover:text-indigo-600 font-bold text-[9px] ml-1 shrink-0 px-1 border-l transition-colors duration-150">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex gap-2">
                        <span class="text-sm shrink-0">ℹ️</span>
                        <p class="text-[10px] text-amber-800 font-semibold leading-relaxed">
                            <strong>Note:</strong> DNS changes take time to propagate across servers worldwide. It might take anywhere between 1 to 24 hours for your store to connect successfully.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Javascript Actions -->
    <script>
        function copyText(elementId, btn) {
            const codeEl = document.getElementById(elementId);
            if (!codeEl) return;
            
            const text = codeEl.innerText.trim();
            
            const doCopy = () => {
                if (btn) {
                    const originalText = btn.innerText;
                    btn.innerText = "Copied!";
                    btn.classList.add("text-emerald-600");
                    btn.classList.remove("text-slate-400");
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.classList.remove("text-emerald-600");
                        btn.classList.add("text-slate-400");
                    }, 2000);
                }
                alertCopied();
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    doCopy();
                }).catch(err => {
                    fallbackCopy(text, btn, doCopy);
                });
            } else {
                fallbackCopy(text, btn, doCopy);
            }
        }

        function fallbackCopy(text, btn, doCopy) {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            textarea.style.position = "fixed";
            textarea.style.left = "-9999px";
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand("copy");
                doCopy();
            } catch (err) {
                console.error("Fallback copy failed", err);
            }
            document.body.removeChild(textarea);
        }

        function alertCopied() {
            // Create a small premium floating alert
            const alertBox = document.createElement("div");
            alertBox.className = "fixed bottom-5 right-5 bg-slate-900 text-white text-[11px] font-black px-4 py-2.5 rounded-xl shadow-lg transition duration-300 z-50 flex items-center gap-1.5";
            alertBox.innerHTML = "<span class=\"text-emerald-400\">✓</span> <span>Copied to clipboard!</span>";
            document.body.appendChild(alertBox);
            
            setTimeout(() => {
                alertBox.style.opacity = "0";
                setTimeout(() => {
                    document.body.removeChild(alertBox);
                }, 300);
            }, 2000);
        }
    </script>
</body>
</html>
