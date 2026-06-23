<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Collection - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if(!empty($settings->facebook_pixel_id))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings->facebook_pixel_id }}');
        fbq('track', 'PageView');
    </script>
    @endif

    @if($settings->enable_rtl)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pakeezah-fonts/jameel-noori-nastaleeq@1.0.0/index.css">
    <style>
        html {
            font-size: 115% !important; /* Scale up all rem-based font sizes for Urdu Nastaleeq readability */
        }
        body {
            direction: rtl;
            text-align: right;
            font-family: 'Jameel Noori Nastaleeq', sans-serif !important;
        }
        p, span, h1, h2, h3, h4, h5, h6, a, button, input, select, textarea {
            line-height: 1.6 !important; /* Taller line-height to prevent Nastaleeq descender/ascender overlap */
        }
    </style>
    @endif
    <style>
        .marquee-container {
            overflow: hidden;
            position: relative;
            width: 100%;
            height: 36px;
        }
        .marquee-text {
            position: absolute;
            white-space: nowrap;
            width: max-content;
            height: 100%;
            margin: 0;
            line-height: 36px;
            transform: translateX(100vw);
            animation: scroll-left 20s linear infinite;
        }
        @keyframes scroll-left {
            0%   { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }
        
        .marquee-text-rtl {
            position: absolute;
            white-space: nowrap;
            width: max-content;
            height: 100%;
            margin: 0;
            line-height: 36px;
            transform: translateX(-100%);
            animation: scroll-right 20s linear infinite;
        }
        @keyframes scroll-right {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(100vw); }
        }
    </style>

    @php
        $fontSizeNum = (int) filter_var($settings->announcement_font_size ?? '14px', FILTER_SANITIZE_NUMBER_INT);
        $announcementHeight = $fontSizeNum * 2 + ($settings->enable_rtl ? 20 : 12);
    @endphp
    <style>
        /* Dynamic Stylesheet */
        .marquee-container {
            height: {{ $announcementHeight }}px !important;
        }
        .marquee-text, .marquee-text-rtl {
            line-height: {{ $announcementHeight }}px !important;
        }
        .marquee-text, .marquee-text-rtl {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
        }
        .static-announcement-bar {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
            line-height: {{ $settings->enable_rtl ? '1.8' : '1.5' }} !important;
            padding-top: {{ $fontSizeNum * ($settings->enable_rtl ? 0.8 : 0.6) }}px !important;
            padding-bottom: {{ $fontSizeNum * ($settings->enable_rtl ? 0.8 : 0.6) }}px !important;
        }
        .store-logo-img {
            height: {{ $settings->header_logo_height ?? 56 }}px !important;
            max-width: 180px !important;
        }
        @media (min-width: 768px) {
            .store-logo-img {
                max-width: none !important;
            }
        }
        .store-header {
            background-color: {{ $settings->header_menu_bg ?? '#ffffff' }} !important;
        }
        .header-menu-link {
            color: {{ $settings->header_menu_text ?? '#1f2937' }} !important;
            background-color: transparent;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .header-menu-link:hover, .cart-icon-btn:hover, #mobileMenuBtn:hover {
            color: {{ $settings->header_menu_active_text ?? '#16a34a' }} !important;
        }
        .header-menu-link.active {
            background-color: {{ $settings->header_menu_active_bg ?? '#f3f4f6' }} !important;
            color: {{ $settings->header_menu_active_text ?? '#16a34a' }} !important;
        }
        .btn-primary-custom {
            background-color: {{ $settings->btn_primary_bg ?? '#16a34a' }} !important;
            color: {{ $settings->btn_primary_text ?? '#ffffff' }} !important;
        }
        .btn-primary-custom:hover {
            opacity: 0.9 !important;
        }
        .btn-secondary-custom {
            background-color: {{ $settings->btn_secondary_bg ?? '#1f2937' }} !important;
            color: {{ $settings->btn_secondary_text ?? '#ffffff' }} !important;
        }
        .btn-secondary-custom:hover {
            opacity: 0.9 !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

    @if($settings && $settings->announcement_active)
        @if($settings->announcement_marquee)
            <div class="marquee-container shadow-sm font-bold text-sm" style="background-color: {{ $settings->announcement_bg_color }}; color: {{ $settings->announcement_text_color }};">
                <div class="{{ $settings->enable_rtl ? 'marquee-text-rtl' : 'marquee-text' }}">
                    {{ $settings->announcement_text }}
                </div>
            </div>
        @else
            <div class="text-center font-bold tracking-wide shadow-sm static-announcement-bar" style="background-color: {{ $settings->announcement_bg_color }}; color: {{ $settings->announcement_text_color }};">
                {{ $settings->announcement_text }}
            </div>
        @endif
    @endif

    <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100 store-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ tenant_store_url('/') }}">
                        @if($settings && $settings->header_logo)
                            <img class="w-auto object-contain store-logo-img" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">
                        @else
                            <span class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight" style="color: {{ $settings->header_menu_text ?? '#1f2937' }};">🛍️ {{ strtoupper($tenantId) }}</span>
                        @endif
                    </a>
                </div>
                <nav class="hidden md:flex space-x-4 items-center">
                    @if(is_array($settings->header_menu))
                        @foreach($settings->header_menu as $menuItem)
                            @php
                                $url = $menuItem['url'] ?? '';
                                $isActive = false;
                                if ($url === '/' || $url === '') {
                                    $isActive = request()->is('/');
                                } else {
                                    $isActive = request()->is(trim($url, '/')) || request()->is(trim($url, '/') . '/*') || request()->fullUrl() == url($url);
                                }
                            @endphp
                            <a href="{{ $menuItem['url'] }}" class="header-menu-link text-base font-bold {{ $isActive ? 'active' : '' }}">{{ $menuItem['label'] }}</a>
                        @endforeach
                    @else
                        <a href="{{ tenant_store_url('/') }}" class="header-menu-link text-base font-bold {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        <a href="/collection" class="header-menu-link text-base font-bold {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
                    @endif
                </nav>
                <div class="flex items-center space-x-4">
                    <!-- Account Icon -->
                    <a href="{{ auth('customer')->check() ? '/customer/dashboard' : '/customer/login' }}" 
                       class="transition hover:opacity-85 flex items-center" 
                       style="color: {{ $settings->header_menu_text ?? '#1f2937' }};"
                       title="{{ auth('customer')->check() ? (auth('customer')->user()->name) : ($settings->enable_rtl ? 'اکاؤنٹ' : 'Account') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>

                    <button onclick="openCartDrawer()" class="cart-icon-btn transition flex items-center relative" style="color: {{ $settings->header_menu_text ?? '#1f2937' }};">
                        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span id="cart-badge" class="absolute -top-1.5 -right-1.5 bg-green-600 text-white font-black rounded-full text-[10px] w-4.5 h-4.5 flex items-center justify-center border border-white" style="display: none;">0</span>
                    </button>
                    <button id="mobileMenuBtn" onclick="toggleMobileMenu()" class="md:hidden focus:outline-none" style="color: {{ $settings->header_menu_text ?? '#1f2937' }};">
                        <svg id="hamburgerIcon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="menuPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div id="mobileMenu" class="md:hidden hidden border-t p-4 space-y-2 shadow-lg store-header">
            @if(is_array($settings->header_menu))
                @foreach($settings->header_menu as $menuItem)
                    @php
                        $url = $menuItem['url'] ?? '';
                        $isActive = false;
                        if ($url === '/' || $url === '') {
                            $isActive = request()->is('/');
                        } else {
                            $isActive = request()->is(trim($url, '/')) || request()->is(trim($url, '/') . '/*') || request()->fullUrl() == url($url);
                        }
                    @endphp
                    <a href="{{ $menuItem['url'] }}" class="header-menu-link block font-bold py-2 {{ $isActive ? 'active' : '' }}">{{ $menuItem['label'] }}</a>
                @endforeach
            @else
                <a href="{{ tenant_store_url('/') }}" class="header-menu-link block font-bold py-2 {{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="/collection" class="header-menu-link block font-bold py-2 {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
            @endif

            <!-- Mobile Account Link -->
            <a href="{{ auth('customer')->check() ? '/customer/dashboard' : '/customer/login' }}" 
               class="header-menu-link block font-bold py-2 border-t border-slate-100 mt-2 flex items-center gap-1.5"
               style="color: {{ $settings->header_menu_text ?? '#1f2937' }};">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>{{ auth('customer')->check() ? (auth('customer')->user()->name) : ($settings->enable_rtl ? 'اکاؤنٹ لاگ ان / رجسٹر' : 'Account Login / Register') }}</span>
            </a>
        </div>
    </header>

    <!-- Main Shop Area -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">
        
        <!-- Page Title Banner -->
        @if($settings->collection_show_banner)
            <div class="text-center py-16 px-6 rounded-[2.5rem] mb-12 shadow-sm border border-transparent" style="background-color: {{ $settings->collection_banner_bg ?? '#eff6ff' }}; color: {{ $settings->collection_banner_text_color ?? '#1e3a8a' }};">
                <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-normal">
                    {{ $settings->collection_title ?? ($settings->enable_rtl ? 'ہماری تمام پروڈکٹس' : 'All Products') }}
                </h1>
                @if($settings->collection_subtitle)
                    <p class="font-bold mt-3 opacity-90 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                        {{ $settings->collection_subtitle }}
                    </p>
                @endif
            </div>
        @else
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-normal">
                    {{ $settings->collection_title ?? ($settings->enable_rtl ? 'ہماری تمام پروڈکٹس' : 'All Products') }}
                </h1>
                @if($settings->collection_subtitle)
                    <p class="text-gray-500 font-bold mt-2 text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                        {{ $settings->collection_subtitle }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Filter and Sort Bar -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-8 flex flex-col md:flex-row gap-6 justify-between items-center">
            
            <!-- Filter Form -->
            <form action="/collection" method="GET" id="filterForm" class="w-full flex flex-wrap gap-4 items-center justify-start">
                
                <!-- Availability Filter -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $settings->enable_rtl ? 'دستیابی' : 'Availability' }}:</label>
                    <select name="availability" onchange="document.getElementById('filterForm').submit()" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-xs font-bold outline-none focus:border-green-600 transition">
                        <option value="">{{ $settings->enable_rtl ? 'تمام اشیاء' : 'All Items' }}</option>
                        <option value="in_stock" {{ request('availability') == 'in_stock' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'موجود اشیاء (In Stock)' : 'In Stock Only' }}</option>
                    </select>
                </div>

                <!-- Price Filter -->
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $settings->enable_rtl ? 'زیادہ سے زیادہ قیمت' : 'Max Price' }}:</label>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="e.g. 3000" onchange="document.getElementById('filterForm').submit()" class="w-28 px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-xs font-bold outline-none focus:border-green-600 transition">
                </div>

                <!-- Sort option (carried forward in filter) -->
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">

                @if(request()->filled('availability') || request()->filled('price_max'))
                    <a href="/collection" class="text-xs font-bold text-red-500 hover:underline flex items-center gap-1">
                        ✕ {{ $settings->enable_rtl ? 'صاف کریں' : 'Clear Filters' }}
                    </a>
                @endif
            </form>

            <!-- Sort Form (Submits to collection) -->
            <div class="flex items-center gap-2 w-full md:w-auto shrink-0 justify-end">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider shrink-0">{{ $settings->enable_rtl ? 'ترتیب دیں' : 'Sort By' }}:</label>
                <select onchange="window.location.href='/collection?' + new URLSearchParams({ ...Object.fromEntries(new URLSearchParams(window.location.search)), sort_by: this.value }).toString()" class="px-3 py-2 border border-gray-200 rounded-xl bg-gray-50 text-xs font-bold outline-none focus:border-green-600 transition">
                    <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'تازہ ترین اشیاء' : 'Latest' }}</option>
                    <option value="alpha_asc" {{ request('sort_by') == 'alpha_asc' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'حروفِ تہجی (A-Z)' : 'Alphabetically, A-Z' }}</option>
                    <option value="alpha_desc" {{ request('sort_by') == 'alpha_desc' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'حروفِ تہجی (Z-A)' : 'Alphabetically, Z-A' }}</option>
                    <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'قیمت: کم سے زیادہ' : 'Price, low to high' }}</option>
                    <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>{{ $settings->enable_rtl ? 'قیمت: زیادہ سے کم' : 'Price, high to low' }}</option>
                </select>
            </div>
        </div>

        <!-- Product Grid -->
        @if($products->isEmpty())
            <div class="py-20 text-center text-gray-400 font-bold border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50">
                <span class="text-5xl block mb-4">🔍</span>
                <p class="text-lg">{{ $settings->enable_rtl ? 'اس فلٹر کے مطابق کوئی پروڈکٹ نہیں ملی۔' : 'No products match your filters.' }}</p>
                <a href="/collection" class="mt-4 inline-block btn-primary-custom font-bold px-6 py-2.5 rounded-xl text-xs transition">{{ $settings->enable_rtl ? 'فلٹر صاف کریں' : 'Reset Shop' }}</a>
            </div>
        @else

            @php
                $bundleDeals = $products->filter(fn($p) => $p->is_bundle);
                $discountDeals = $products->filter(fn($p) => !$p->is_bundle && $p->is_discount);
                $regularProducts = $products->filter(fn($p) => !$p->is_bundle && !$p->is_discount);
            @endphp
            <div class="space-y-16">
                <!-- 📦 BUNDLE DEALS SECTION -->
                @if($bundleDeals->isNotEmpty())
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>📦</span>
                                <span>{{ $settings->enable_rtl ? 'بنڈل ڈیلز (Bundle Deals)' : 'Bundle Deals' }}</span>
                            </h2>
                            <p class="text-xs text-gray-500 font-semibold mt-1">{{ $settings->enable_rtl ? 'ان بنڈلز کے ساتھ بڑی بچت کریں' : 'Save big with these curated bundle packages' }}</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($bundleDeals as $product)
                                @php
                                    $displayPrice = $product->price;
                                    $displayCompare = $product->compare_price;
                                    $isFrom = false;

                                    if ($product->is_bundle && !empty($product->bundle_options) && is_array($product->bundle_options)) {
                                        $displayPrice = $product->bundle_options[0]['price'];
                                        $displayCompare = $product->bundle_options[0]['compare_price'] ?? null;
                                        $isFrom = true;
                                    }
                                    
                                    $pct = null;
                                    if ($displayCompare && $displayCompare > $displayPrice) {
                                        $pct = round((($displayCompare - $displayPrice) / $displayCompare) * 100);
                                    }
                                @endphp
                                <a href="/product/{{ $product->id }}" class="block bg-white rounded-[2rem] shadow-sm border border-gray-150 p-4 hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
                                    <div>
                                        <!-- Image Container -->
                                        <div class="w-full aspect-square overflow-hidden rounded-[1.5rem] relative bg-gray-50">
                                            @if($product->image)
                                                <img src="{{ tenant_asset($product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">No Image</div>
                                            @endif
                                            @if($pct)
                                                <div class="absolute bottom-0 left-0 bg-[#16a34a] text-white text-[11px] font-black px-3 py-1.5 rounded-tr-2xl rounded-bl-2xl flex items-center gap-1 z-10 shadow-sm uppercase tracking-wider">
                                                    🏷️ {{ $settings->enable_rtl ? 'بچت' : 'SAVE' }} {{ $pct }}%
                                                </div>
                                            @else
                                                <div class="absolute bottom-0 left-0 bg-indigo-600 text-white text-[10px] font-black px-3 py-1.5 rounded-tr-2xl rounded-bl-2xl flex items-center gap-1 z-10 shadow-sm uppercase tracking-wider">
                                                    📦 {{ $settings->enable_rtl ? 'بنڈل ڈیل' : 'BUNDLE DEAL' }}
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Details -->
                                        <div class="pt-6 pb-2 text-center">
                                            <h4 class="text-base md:text-lg font-black text-gray-900 mb-2 leading-snug text-center group-hover:underline decoration-2 underline-offset-4 decoration-gray-900 transition duration-150">
                                                {{ $product->name }}
                                            </h4>
                                            
                                            <div class="flex items-center justify-center gap-2.5 flex-wrap">
                                                <span class="text-base md:text-lg font-black text-[#16a34a]">
                                                    @if($isFrom)
                                                        {{ $settings->enable_rtl ? 'سے' : 'From' }}
                                                    @endif
                                                    Rs. {{ number_format($displayPrice, 2) }}
                                                </span>
                                                @if($displayCompare && $displayCompare > $displayPrice)
                                                    <span class="text-xs md:text-sm font-bold text-gray-400 line-through">
                                                        Rs. {{ number_format($displayCompare, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 🔥 SPECIAL DISCOUNT DEALS SECTION -->
                @if($discountDeals->isNotEmpty())
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>🔥</span>
                                <span>{{ $settings->enable_rtl ? 'خصوصی ڈسکاؤنٹ ڈیلز (Special Discounts)' : 'Special Discount Deals' }}</span>
                            </h2>
                            <p class="text-xs text-gray-500 font-semibold mt-1">{{ $settings->enable_rtl ? 'محدود وقت کے لیے خصوصی بچت کی پیشکش' : 'Limited-time special offers and mega discounts' }}</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($discountDeals as $product)
                                @php
                                    $displayPrice = $product->final_price;
                                    $displayCompare = $product->price;
                                    $pct = round((($displayCompare - $displayPrice) / $displayCompare) * 100);
                                @endphp
                                <a href="/product/{{ $product->id }}" class="block bg-white rounded-[2rem] shadow-sm border border-gray-150 p-4 hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
                                    <div>
                                        <!-- Image Container -->
                                        <div class="w-full aspect-square overflow-hidden rounded-[1.5rem] relative bg-gray-50">
                                            @if($product->image)
                                                <img src="{{ tenant_asset($product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">No Image</div>
                                            @endif
                                            @if($pct)
                                                <div class="absolute bottom-0 left-0 bg-[#16a34a] text-white text-[11px] font-black px-3 py-1.5 rounded-tr-2xl rounded-bl-2xl flex items-center gap-1 z-10 shadow-sm uppercase tracking-wider">
                                                    🏷️ {{ $settings->enable_rtl ? 'بچت' : 'SAVE' }} {{ $pct }}%
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Details -->
                                        <div class="pt-6 pb-2 text-center">
                                            <h4 class="text-base md:text-lg font-black text-gray-900 mb-2 leading-snug text-center group-hover:underline decoration-2 underline-offset-4 decoration-gray-900 transition duration-150">
                                                {{ $product->name }}
                                            </h4>
                                            
                                            <div class="flex items-center justify-center gap-2.5 flex-wrap">
                                                <span class="text-base md:text-lg font-black text-[#16a34a]">
                                                    Rs. {{ number_format($displayPrice, 2) }}
                                                </span>
                                                @if($displayCompare && $displayCompare > $displayPrice)
                                                    <span class="text-xs md:text-sm font-bold text-gray-400 line-through">
                                                        Rs. {{ number_format($displayCompare, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ✨ REGULAR PRODUCTS SECTION -->
                @if($regularProducts->isNotEmpty())
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>✨</span>
                                <span>{{ $settings->enable_rtl ? 'عام پروڈکٹس (Regular Products)' : 'Our Products' }}</span>
                            </h2>
                            <p class="text-xs text-gray-500 font-semibold mt-1">{{ $settings->enable_rtl ? 'ہماری بہترین پروڈکٹس کی رینج' : 'Browse our catalog of high-quality items' }}</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($regularProducts as $product)
                                @php
                                    $displayPrice = $product->price;
                                    $displayCompare = $product->compare_price;
                                    
                                    $pct = null;
                                    if ($displayCompare && $displayCompare > $displayPrice) {
                                        $pct = round((($displayCompare - $displayPrice) / $displayCompare) * 100);
                                    }
                                @endphp
                                <a href="/product/{{ $product->id }}" class="block bg-white rounded-[2rem] shadow-sm border border-gray-150 p-4 hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
                                    <div>
                                        <!-- Image Container -->
                                        <div class="w-full aspect-square overflow-hidden rounded-[1.5rem] relative bg-gray-50">
                                            @if($product->image)
                                                <img src="{{ tenant_asset($product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 font-bold">No Image</div>
                                            @endif
                                            @if($pct)
                                                <div class="absolute bottom-0 left-0 bg-[#16a34a] text-white text-[11px] font-black px-3 py-1.5 rounded-tr-2xl rounded-bl-2xl flex items-center gap-1 z-10 shadow-sm uppercase tracking-wider">
                                                    🏷️ {{ $settings->enable_rtl ? 'بچت' : 'SAVE' }} {{ $pct }}%
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Details -->
                                        <div class="pt-6 pb-2 text-center">
                                            <h4 class="text-base md:text-lg font-black text-gray-900 mb-2 leading-snug text-center group-hover:underline decoration-2 underline-offset-4 decoration-gray-900 transition duration-150">
                                                {{ $product->name }}
                                            </h4>
                                            
                                            <div class="flex items-center justify-center gap-2.5 flex-wrap">
                                                <span class="text-base md:text-lg font-black text-[#16a34a]">
                                                    Rs. {{ number_format($displayPrice, 2) }}
                                                </span>
                                                @if($displayCompare && $displayCompare > $displayPrice)
                                                    <span class="text-xs md:text-sm font-bold text-gray-400 line-through">
                                                        Rs. {{ number_format($displayCompare, 2) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </main>

    <footer style="background-color: {{ $settings->footer_bg_color ?? '#4CAF50' }}; color: {{ $settings->footer_text_color ?? '#ffffff' }};" class="pt-20 pb-16 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12">
            <div>
                <h4 class="text-2xl font-black mb-6">About us</h4>
                <p class="text-sm leading-relaxed mb-6 opacity-90 font-medium">{{ $settings->footer_about ?? 'Empowering your health naturally with safe and effective herbal supplements. Trusted by thousands.' }}</p>
                @if($settings->footer_email) <p class="text-sm mb-3"><strong class="font-black opacity-100">Mail:</strong> <span class="opacity-90">{{ $settings->footer_email }}</span></p> @endif
                @if($settings->footer_phone) <p class="text-sm mb-3"><strong class="font-black opacity-100">Contact:</strong><br><span class="opacity-90">{{ $settings->footer_phone }}</span></p> @endif
                @if($settings->footer_whatsapp) <p class="text-sm mb-3"><strong class="font-black opacity-100">WhatsApp:</strong><br><span class="opacity-90">{{ $settings->footer_whatsapp }}</span></p> @endif
                @if($settings->footer_address) <p class="text-sm mb-3 mt-5"><strong class="font-black opacity-100">Address:</strong> <span class="opacity-90">{{ $settings->footer_address }}</span></p> @endif
            </div>
            <div>
                <h4 class="text-2xl font-black mb-6">Quick links</h4>
                <ul class="space-y-4 text-sm font-bold opacity-90">
                    @if(is_array($settings->footer_quick_links))
                        @foreach($settings->footer_quick_links as $link)
                            <li><a href="{{ $link['url'] }}" class="hover:opacity-100 hover:underline transition">{{ $link['label'] }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ tenant_store_url('/') }}" class="hover:opacity-100 hover:underline transition">Home</a></li>
                        <li><a href="/collection" class="hover:opacity-100 hover:underline transition">Shop</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-2xl font-black mb-6">Policies</h4>
                <ul class="space-y-4 text-sm font-bold opacity-90">
                    @if(is_array($settings->footer_policies_links))
                        @foreach($settings->footer_policies_links as $link)
                            <li><a href="{{ $link['url'] }}" class="hover:opacity-100 hover:underline transition">{{ $link['label'] }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#" class="hover:opacity-100 hover:underline transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:opacity-100 hover:underline transition">Refund Policy</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-2xl font-black mb-6 leading-tight">Subscribe to our emails</h4>
                <p class="text-sm mb-6 opacity-90 leading-relaxed font-medium">{{ $settings->footer_newsletter_text ?? 'Join our email list for exclusive offers and the latest news.' }}</p>
                <form class="newsletter-signup-form flex flex-col gap-3">
                    <input type="email" placeholder="Email Address" required class="w-full bg-black/10 border-0 rounded-xl py-4 px-5 text-sm placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white transition font-bold">
                    <button type="submit" style="color: {{ $settings->footer_bg_color ?? '#4CAF50' }};" class="w-full bg-white font-black py-4 px-5 rounded-xl hover:bg-gray-100 transition shadow-lg transform hover:-translate-y-1">Subscribe Now</button>
                </form>
            </div>
        </div>
    </footer>
    <div style="background-color: {{ $settings->footer_bottom_bg_color ?? '#1B5E20' }}; color: {{ $settings->footer_bottom_text_color ?? '#ffffff' }};" class="py-6 text-center text-xs font-bold">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 opacity-90">
            &copy; {{ date('Y') }}, {{ $settings->footer_copyright ?? strtoupper($tenantId) . ' All rights reserved' }}
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const menuPath = document.getElementById('menuPath');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    menu.classList.remove('hidden');
                    if (menuPath) menuPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
                } else {
                    menu.classList.add('hidden');
                    if (menuPath) menuPath.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
                }
            }
        }
    </script>
    @include('tenant.partials.cart-drawer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.newsletter-signup-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = form.querySelector('input[type="email"]');
                    const email = input ? input.value : '';
                    const btn = form.querySelector('button[type="submit"]');
                    if (!email) return;

                    if (btn) btn.disabled = true;

                    fetch('/newsletter-subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (btn) btn.disabled = false;
                        if (res.success) {
                            if (input) input.value = '';
                            showToastNotification(res.message, 'success');
                        } else {
                            showToastNotification(res.message, 'error');
                        }
                    })
                    .catch(err => {
                        if (btn) btn.disabled = false;
                        showToastNotification('Something went wrong, please try again.', 'error');
                    });
                });
            });

            function showToastNotification(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-5 right-5 text-white rounded-2xl shadow-2xl p-5 flex items-start gap-3.5 max-w-sm transform translate-y-10 opacity-0 transition-all duration-300 z-[9999] ' + 
                    (type === 'success' ? 'bg-slate-900 border border-slate-800' : 'bg-red-900 border border-red-800');
                toast.innerHTML = `
                    <div class="p-1 rounded-lg ${type === 'success' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'} shrink-0 text-lg">
                        ${type === 'success' ? '✨' : '❌'}
                    </div>
                    <div class="flex-grow space-y-0.5" style="text-align: left; direction: ltr;">
                        <h5 class="text-xs font-black tracking-wide uppercase">${type === 'success' ? 'Success' : 'Error'}</h5>
                        <p class="text-[10px] opacity-80 leading-relaxed font-bold">${message}</p>
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.classList.remove('translate-y-10', 'opacity-0');
                }, 100);
                setTimeout(() => {
                    toast.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            }
        });
    </script>
        @include('tenant.partials.whatsapp-widget')
    </body>
</html>
