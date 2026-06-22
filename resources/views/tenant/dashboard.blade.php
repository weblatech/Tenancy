<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant('name') ?? strtoupper($tenantId) }} — Store Admin Dashboard</title>
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
        .card-hover {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(99, 102, 241, 0.12);
            border-color: #6366f1;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased bg-slate-50/50 relative overflow-x-hidden">

    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>

    <!-- Top Premium Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md relative z-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Branding & Status -->
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-indigo-600 to-violet-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-white font-extrabold text-sm tracking-tight uppercase flex items-center gap-1.5">
                            {{ tenant('name') ?? $tenantId }} <span class="bg-indigo-600/20 text-indigo-400 text-[9px] px-2 py-0.5 rounded-full font-bold border border-indigo-500/30">Active Store</span>
                        </h1>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Munaa Admin Control Panel</p>
                    </div>
                </div>

                <!-- Right: Quick Actions -->
                <div class="flex items-center gap-4">
                    <a href="{{ tenant_store_url() }}" target="_blank" class="hidden md:flex items-center gap-1.5 text-slate-400 hover:text-white text-xs font-bold transition duration-150">
                        <span>View Storefront</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <a href="/shop/settings" class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-[11px] font-black px-4 py-2.5 rounded-xl transition duration-200 shadow-md shadow-indigo-600/10 flex items-center gap-1.5">
                        🎨 <span>Customize Store Theme</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-12">

        <!-- Page Header -->
        <div class="mb-12">
            <div class="flex items-center gap-3">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Merchant Dashboard</h2>
                @php
                    $planColors = [
                        'starter' => 'bg-sky-100 text-sky-700 border-sky-200',
                        'growth' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'enterprise' => 'bg-violet-100 text-violet-700 border-violet-200',
                    ];
                    $planLabels = [
                        'starter' => 'Starter',
                        'growth' => 'Growth',
                        'enterprise' => 'Enterprise',
                    ];
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $planColors[tenant_plan()] ?? $planColors['starter'] }}">
                    {{ $planLabels[tenant_plan()] ?? 'Starter' }} Plan
                </span>
            </div>
            <p class="text-slate-500 font-medium text-sm mt-1">Welcome back! Manage products, process orders, and inspect your business metrics.</p>
            @if(tenant('subscription_status') === 'trialing')
                @php
                    $daysLeft = max(0, \Carbon\Carbon::parse(tenant('subscription_ends_at'))->diffInDays(now()));
                @endphp
                <p class="text-amber-600 font-semibold text-sm mt-2">
                    ⏱ Free trial: {{ $daysLeft }} days remaining
                    <a href="#" class="text-indigo-600 hover:underline ml-2">Upgrade now</a>
                </p>
            @endif
        </div>

        <!-- Section Selection Cards (Opens separate pages) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            <!-- Card 1: Products Catalog -->
            <a href="/shop/products" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-blue-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-blue-600 transition">Products</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Manage details, stock & pricing</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        <span class="text-slate-800 font-black">{{ $productCount }}</span> Items
                    </div>
                    <span class="text-[10px] font-black text-blue-600 bg-blue-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-blue-100 transition duration-150">Manage →</span>
                </div>
            </a>

            <!-- Card 2: Orders Manager -->
            <a href="/shop/orders" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-emerald-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-emerald-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-emerald-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-emerald-600 transition">Orders</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Fulfill incoming store orders</p>
                        </div>
                    </div>
                    @if($pendingOrdersCount > 0)
                        <span class="text-[9px] font-black text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md uppercase tracking-wider animate-pulse text-right">5 Pending</span>
                    @endif
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        <span class="text-slate-800 font-black">{{ count($orders) }}</span> Total
                    </div>
                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-emerald-100 transition duration-150">Fulfill →</span>
                </div>
            </a>

            <!-- Card 3: Customers Manager -->
            <a href="/shop/customers" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-indigo-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-indigo-600 transition">Customers</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Manage and view buyer profiles</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        <span class="text-slate-800 font-black">{{ $customerCount }}</span> Total
                    </div>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-indigo-100 transition duration-150">View →</span>
                </div>
            </a>

            <!-- Card 4: Theme Customizer -->
            <a href="/shop/settings" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-pink-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-pink-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-pink-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pink-600 transition">Customize</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Configure sections & styles</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Live Editor
                    </div>
                    <span class="text-[10px] font-black text-pink-600 bg-pink-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-pink-100 transition duration-150">Design →</span>
                </div>
            </a>

            <!-- Card 5: Pages Manager -->
            <a href="/shop/settings#pages" onclick="window.location.href='/shop/settings'; localStorage.setItem('activeTab', 'pages'); return false;" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-orange-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-orange-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-orange-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-orange-600 transition">Pages</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Policies, contacts & info</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        <span class="text-slate-800 font-black">{{ count($pages) }}</span> Active Pages
                    </div>
                    <span class="text-[10px] font-black text-orange-600 bg-orange-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-orange-100 transition duration-150">Create →</span>
                </div>
            </a>

            <!-- Card 6: Messages Manager -->
            <a href="/shop/messages" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-blue-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-blue-600 transition">Messages</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Customer contact form messages</p>
                        </div>
                    </div>
                    @php
                        $msgsFile = storage_path('app/contact_messages_' . tenant('id') . '.json');
                        $msgsCount = 0;
                        if (file_exists($msgsFile)) {
                            $msgsCount = count(json_decode(file_get_contents($msgsFile), true) ?? []);
                        }
                    @endphp
                    @if($msgsCount > 0)
                        <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md uppercase tracking-wider">1 Inbox</span>
                    @endif
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Inbox Manager
                    </div>
                    <span class="text-[10px] font-black text-blue-600 bg-blue-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-blue-100 transition duration-150">View →</span>
                </div>
            </a>

            <!-- Card 7: Subscribers Manager -->
            <a href="/shop/subscribers" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-teal-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-teal-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-teal-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-teal-600 transition">Subscribers</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Newsletter subscriber emails</p>
                        </div>
                    </div>
                    @php
                        $subsFile = storage_path('app/subscribers_' . tenant('id') . '.json');
                        $subsCount = 0;
                        if (file_exists($subsFile)) {
                            $subsCount = count(json_decode(file_get_contents($subsFile), true) ?? []);
                        }
                    @endphp
                    @if($subsCount > 0)
                        <span class="text-[9px] font-black text-teal-600 bg-teal-50 px-2.5 py-1 rounded-md uppercase tracking-wider">1 Email</span>
                    @endif
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Marketing List
                    </div>
                    <span class="text-[10px] font-black text-teal-600 bg-teal-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-teal-100 transition duration-150">Manage →</span>
                </div>
            </a>

            <!-- Card 8: Payment & Delivery Settings -->
            <a href="/shop/payments" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-blue-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-blue-600 transition">Payment Settings</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">COD, bank credentials & shipping</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Delivery & Gateway
                    </div>
                    <span class="text-[10px] font-black text-blue-600 bg-blue-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-blue-100 transition duration-150">Manage →</span>
                </div>
            </a>

            <!-- Card 9: Custom Domain Settings -->
            @if(plan_feature('custom_domain'))
            <a href="/shop/domains" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-indigo-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-indigo-600 transition">Domains</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Link your own custom domain</p>
                        </div>
                    </div>
                    @php
                        $domainsCount = tenant()->domains()->count();
                    @endphp
                    @if($domainsCount > 0)
                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md uppercase tracking-wider">1 Domain</span>
                    @endif
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Domain Settings
                    </div>
                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-indigo-100 transition duration-150">Manage →</span>
                </div>
            </a>
            @else
            <div class="group relative card-premium rounded-3xl p-6 flex flex-col justify-between h-40 overflow-hidden opacity-60">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-slate-300"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-slate-400 text-white w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Domains</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Link your own custom domain</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md uppercase tracking-wider">Growth+</span>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Upgrade to access
                    </div>
                    <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3.5 py-1.5 rounded-lg">Locked</span>
                </div>
            </div>
            @endif

            <!-- Card 10: Social Media & Tracking -->
            @if(plan_feature('social_tracking'))
            <a href="/shop/social" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-pink-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-pink-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-pink-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-pink-600 transition">Social & Tracking</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Pixels, ads & social profiles</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Ad Platforms & Analytics
                    </div>
                    <span class="text-[10px] font-black text-pink-600 bg-pink-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-pink-100 transition duration-150">Manage →</span>
                </div>
            </a>
            @else
            <div class="group relative card-premium rounded-3xl p-6 flex flex-col justify-between h-40 overflow-hidden opacity-60">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-slate-300"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-slate-400 text-white w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Social & Tracking</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Pixels, ads & social profiles</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md uppercase tracking-wider">Growth+</span>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Upgrade to access
                    </div>
                    <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3.5 py-1.5 rounded-lg">Locked</span>
                </div>
            </div>
            @endif

            <!-- Card 11: WhatsApp Chat -->
            @if(plan_feature('whatsapp_chat'))
            <a href="/shop/whatsapp-chat" class="group relative card-premium rounded-3xl p-6 card-hover flex flex-col justify-between h-40 overflow-hidden cursor-pointer no-underline">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-green-500"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-md shadow-green-500/25 shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-green-600 transition">WhatsApp Chat</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Chat with customers & view history</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Customer Conversations
                    </div>
                    <span class="text-[10px] font-black text-green-600 bg-green-50/80 px-3.5 py-1.5 rounded-lg group-hover:bg-green-100 transition duration-150">Open Chat →</span>
                </div>
            </a>
            @else
            <div class="group relative card-premium rounded-3xl p-6 flex flex-col justify-between h-40 overflow-hidden opacity-60">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-slate-300"></div>
                <div class="flex items-start justify-between w-full">
                    <div class="flex items-center gap-4">
                        <div class="bg-slate-400 text-white w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">WhatsApp Chat</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Chat with customers & view history</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md uppercase tracking-wider">Growth+</span>
                </div>
                <div class="flex items-center justify-between w-full pt-3 border-t border-slate-100">
                    <div class="text-xs font-bold text-slate-500">
                        Upgrade to access
                    </div>
                    <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-3.5 py-1.5 rounded-lg">Locked</span>
                </div>
            </div>
            @endif

        </div>
            
            <!-- Quick Link: Add Product -->
            <div class="bg-gradient-to-br from-indigo-600 to-violet-600 text-white rounded-3xl p-6 md:p-8 flex items-center justify-between shadow-xl shadow-indigo-600/15">
                <div>
                    <h3 class="font-black text-xl mb-1.5">Add New Product</h3>
                    <p class="text-indigo-200 text-xs font-medium max-w-sm leading-relaxed">Instantly add items with photos, custom pricing, and adjustable stock counts.</p>
                </div>
                <a href="/shop/products" class="shrink-0 bg-white text-indigo-700 font-extrabold text-xs px-5 py-3.5 rounded-xl hover:bg-indigo-50 transition shadow-lg shadow-indigo-900/10 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Product</span>
                </a>
            </div>

            <!-- Quick Link: View Storefront -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 md:p-8 flex items-center justify-between shadow-xl shadow-slate-900/15">
                <div>
                    <h3 class="font-black text-xl mb-1.5">Preview Storefront</h3>
                    <p class="text-slate-400 text-xs font-medium max-w-sm leading-relaxed">View your active online store layout as customers see it.</p>
                </div>
                <a href="{{ tenant_store_url() }}" target="_blank" class="shrink-0 bg-white text-slate-900 font-extrabold text-xs px-5 py-3.5 rounded-xl hover:bg-slate-100 transition shadow-lg flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Open Store</span>
                </a>
            </div>

        </div>

    </div>

</body>
</html>