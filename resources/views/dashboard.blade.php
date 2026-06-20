<x-app-layout>
    <!-- Custom styling & fonts for Shopify-like interface -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        .font-sans-dashboard {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .font-heading-dashboard {
            font-family: 'Outfit', sans-serif !important;
        }
        
        /* Premium Hero Panel styling */
        .welcome-hero-panel {
            background-color: #ffffff !important;
            border-radius: 28px !important;
            border: 1px solid #f1f5f9 !important;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04) !important;
            padding: 2.5rem !important;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 640px) {
            .welcome-hero-panel {
                padding: 3.5rem !important;
            }
        }
        
        .welcome-title {
            color: #0f172a !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            font-size: 2.6rem !important;
            line-height: 2.9rem !important;
        }
        .welcome-description {
            color: #334155 !important;
            font-size: 1.05rem !important;
            line-height: 1.75rem !important;
            font-weight: 500 !important;
        }

        /* 3-Column Launch Cards styling */
        .launch-card {
            background-color: #ffffff !important;
            border-radius: 24px !important;
            border: 1px solid #f1f5f9 !important;
            box-shadow: 0 4px 20px -8px rgba(0, 0, 0, 0.03) !important;
            padding: 2.25rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            height: 100% !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .launch-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.06) !important;
            border-color: #e2e8f0 !important;
        }

        .launch-card-icon-wrapper {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.75rem;
        }
        
        .launch-card-title {
            color: #0f172a !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1.45rem !important;
            margin-bottom: 0.875rem !important;
        }
        
        .launch-card-description {
            color: #475569 !important;
            font-size: 0.95rem !important;
            line-height: 1.55rem !important;
            font-weight: 500 !important;
            margin-bottom: 1.75rem !important;
            flex-grow: 1 !important;
        }

        /* Badge and pill styling */
        .detail-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }
        .detail-pill:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* Buttons custom classes */
        .btn-green-launch {
            background-color: #004c3f !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-align: center;
            padding: 0.95rem 1.5rem !important;
            border-radius: 12px !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 6px -1px rgba(0, 76, 63, 0.1) !important;
            text-decoration: none !important;
        }
        .btn-green-launch:hover {
            background-color: #00382f !important;
            box-shadow: 0 10px 15px -3px rgba(0, 76, 63, 0.2) !important;
        }

        .btn-indigo-launch {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-align: center;
            padding: 0.95rem 1.5rem !important;
            border-radius: 12px !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1) !important;
            text-decoration: none !important;
        }
        .btn-indigo-launch:hover {
            background-color: #4338ca !important;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2) !important;
        }

        .btn-white-launch {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            font-weight: 700 !important;
            text-align: center;
            padding: 0.95rem 1.5rem !important;
            border-radius: 12px !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            text-decoration: none !important;
        }
        .btn-white-launch:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }

        .btn-violet-launch {
            background-color: #8b5cf6 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-align: center;
            padding: 0.95rem 1.5rem !important;
            border-radius: 12px !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.1) !important;
            text-decoration: none !important;
        }
        .btn-violet-launch:hover {
            background-color: #7c3aed !important;
            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.2) !important;
        }
    </style>

    <div class="py-12 bg-slate-50/50 min-h-[calc(100vh-65px)] font-sans-dashboard relative overflow-hidden">
        <!-- Glowing background abstract shapes for premium look -->
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-emerald-50/50 blur-3xl -z-10"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-slate-100/60 blur-3xl -z-10"></div>

        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12 space-y-8">
            
            <!-- Welcome Banner Header -->
            <div class="flex flex-col items-center text-center gap-4 border-b border-slate-200/60 pb-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight font-heading-dashboard">
                        Welcome, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p class="text-base text-slate-500 mt-2 font-semibold">
                        Real-time storefront overview and administration hub.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    @if(Auth::user()->tenant_id)
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-bold shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active & Live
                        </div>
                    @endif
                    
                    <!-- Quick Settings Icon Link -->
                    <a href="{{ route('profile.edit') }}" title="Edit Profile & Store Name" class="p-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-900 transition shadow-sm hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.992a7.723 7.723 0 0 1 0-.255c-.008-.379-.153-.75-.43-.992l1.004-.827c.424-.35.534-.954.26-1.43l-1.298-2.247a1.125 1.125 0 0 1-1.369-.491l-1.217.456c-.355.133-.75.072-1.076-.124a6.47 6.47 0 0 1-.22-.128c-.331-.183-.581-.495-.644-.869l-.213 1.281c-.09-.543-.56-.94-1.11-.94h-2.594c-.55 0-1.019.398-1.11.94l-.213 1.281c-.062.374-.312.686-.644.87a6.52 6.52 0 0 1-.22.127c-.325.196-.72.257-1.076.124l-1.217-.456a1.125 1.125 0 0 1-1.369.49l-1.297 2.247a1.125 1.125 0 0 1 .26 1.43l1.004.827c.292.24.437.613.43.991 0 .085.004.17.01.255.007.38-.138.751-.43.992l-1.004.827a1.125 1.125 0 0 1-.26 1.43l1.297 2.247a1.125 1.125 0 0 1 1.37.491l1.216-.456c.356-.133.751-.072 1.076.124.072.044.146.086.22.128.332.183.582.495.644.869l.214 1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </a>
                </div>
            </div>

            @if(Auth::user()->tenant_id)
                @php
                    $tenant = \App\Models\Tenant::find(Auth::user()->tenant_id);
                    $storeName = $tenant ? ($tenant->name ?? ucfirst($tenant->id) . ' Store') : 'My Store';
                @endphp

                <!-- Welcome Hero Card -->
                <div class="welcome-hero-panel space-y-6 text-center">
                    <div class="space-y-4">
                        <div class="flex items-center justify-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-800 text-[10px] font-bold tracking-widest uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Platform Online
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-800 text-[10px] font-bold tracking-widest uppercase">
                                Multi-Tenant Mode
                            </span>
                        </div>
                        
                        <h2 class="welcome-title leading-tight">
                            Welcome to ShopTenancy, {{ Auth::user()->name }}! 🚀
                        </h2>
                        
                        <p class="welcome-description max-w-3xl mx-auto">
                            ShopTenancy is an enterprise-grade multi-tenant e-commerce engine. Your store is provisioned inside an isolated SQLite database environment to ensure maximum performance, security, and scalability. Below are your quick launch tools to configure your storefront, catalog, and designs.
                        </p>
                    </div>
                    
                    <!-- Details ribbon -->
                    <div class="flex flex-wrap items-center justify-center gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ tenant_store_url() }}" target="_blank" class="detail-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-indigo-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.003 9.003 0 0 1 8.716 6.747M12 3a9.003 9.003 0 0 0-8.716 6.747M3 10.5h18" />
                            </svg>
                            <span>{{ request()->getHost() }}</span>
                        </a>
                        
                        <div class="detail-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 .621-.504 1.125-1.125 1.125H4.875c-.621 0-1.125-.504-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125h14.25c.621 0 1.125.504 1.125 1.125v.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 10.5c0 .621-.504 1.125-1.125 1.125H4.875c-.621 0-1.125-.504-1.125-1.125v-.75c0-.621.504-1.125 1.125-1.125h14.25c.621 0 1.125.504 1.125 1.125v.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.625c0 .621-.504 1.125-1.125 1.125H4.875c-.621 0-1.125-.504-1.125-1.125v-.75c0-.621.504-1.125 1.125-1.125h14.25c.621 0 1.125.504 1.125 1.125v.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 18.75c0 .621-.504 1.125-1.125 1.125H4.875c-.621 0-1.125-.504-1.125-1.125v-.75c0-.621.504-1.125 1.125-1.125h14.25c.621 0 1.125.504 1.125 1.125v.75z" />
                            </svg>
                            <span>SQLite (Database Isolated)</span>
                        </div>

                        <div class="detail-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-amber-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span>Active Merchant Storefront</span>
                        </div>
                    </div>
                </div>

                <!-- 4-Column Launch Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
                    <!-- Card 1: Store Admin Portal -->
                    <div class="launch-card">
                        <div>
                            <div class="launch-card-icon-wrapper bg-violet-50 text-violet-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                </svg>
                            </div>
                            <h3 class="launch-card-title">Store Admin Portal</h3>
                            <p class="launch-card-description">
                                Manage your entire store from the central control panel. Process customer orders, view analytics statistics, manage customer reviews, and edit system setups.
                            </p>
                        </div>
                        <a href="{{ tenant_store_url('shop') }}" target="_blank" class="btn-violet-launch">
                            🚀 Go to My Store Admin
                        </a>
                    </div>

                    <!-- Card 2: Theme & Visual Customizer -->
                    <div class="launch-card">
                        <div>
                            <div class="launch-card-icon-wrapper bg-emerald-50 text-emerald-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-2.012.324l-3.322 1.9a3 3 0 0 0-1.1 4.028l.25.432a3 3 0 0 0 4.1 1.1l3.245-1.855a3 3 0 0 0 1.1-4.028l-.25-.432a3 3 0 0 0-2.013-1.471Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.417 8.25-6.52 6.52c-.63.63-.185 1.707.707 1.707H15.75V19.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707" />
                                </svg>
                            </div>
                            <h3 class="launch-card-title">Theme Customizer</h3>
                            <p class="launch-card-description">
                                Design your storefront layout. Customize announcement texts, headers, logo banners, hero sliders, WhatsApp checkout settings, and toggle RTL script support.
                            </p>
                        </div>
                        <a href="{{ tenant_store_url('shop/settings') }}" target="_blank" class="btn-green-launch">
                            🎨 Design Storefront Layout
                        </a>
                    </div>

                    <!-- Card 3: Catalog Manager -->
                    <div class="launch-card">
                        <div>
                            <div class="launch-card-icon-wrapper bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <h3 class="launch-card-title">Products Catalog</h3>
                            <p class="launch-card-description">
                                Add products, upload high-definition listing photos, configure product comparisons (sale pricing labels), track inventory stocks, and write engaging descriptions.
                            </p>
                        </div>
                        <a href="{{ tenant_store_url('shop/add-product') }}" target="_blank" class="btn-indigo-launch">
                            📦 Manage Product Listings
                        </a>
                    </div>

                    <!-- Card 4: Preview Storefront -->
                    <div class="launch-card">
                        <div>
                            <div class="launch-card-icon-wrapper bg-amber-50 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                            </div>
                            <h3 class="launch-card-title">Live Preview</h3>
                            <p class="launch-card-description">
                                View your storefront live as it appears to customers. Check responsiveness, verify the design layout, and test order checkouts on your subdomain.
                            </p>
                        </div>
                        <a href="{{ tenant_store_url() }}" target="_blank" class="btn-white-launch">
                            🌐 Launch Storefront
                        </a>
                    </div>
                </div>
            @else
                <!-- No Store Warning Card -->
                <div class="bg-rose-50 border border-rose-100 rounded-3xl p-10 text-center space-y-4 shadow-sm relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-rose-500/5 rounded-full blur-2xl"></div>
                    <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-800 flex items-center justify-center mx-auto text-2xl shadow-sm">⚠️</div>
                    <h2 class="text-xl font-bold text-slate-900 font-heading-dashboard">Store configuration missing</h2>
                    <p class="text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                        We couldn't detect a sub-domain linked to this user profile. Please contact administrators to configure a merchant space.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>