<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ strtoupper($tenantId) }}</title>
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
        html {
            scroll-behavior: smooth;
        }
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

    <!-- Product Details Main Section -->
    <main class="w-full bg-white flex-grow py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
                <!-- Left Side: Product Image & Gallery (Sticky on desktop) -->
                <div class="flex flex-col gap-4 w-full md:sticky md:top-28">
                <!-- Main Image Container -->
                <div class="bg-gray-50 rounded-3xl shadow-sm border border-gray-100 w-full aspect-square relative overflow-hidden">
                    @if($product->image)
                        <img id="main-product-image" src="{{ tenant_asset($product->image) }}" class="w-full h-full object-cover rounded-3xl transition duration-300">
                    @else
                        <div class="h-80 w-full bg-gray-100 rounded-2xl flex items-center justify-center text-gray-400 font-bold">{{ $settings->enable_rtl ? 'تصویر موجود نہیں ہے' : 'No Image Available' }}</div>
                    @endif
                    <span class="absolute top-4 right-4 bg-green-50 text-green-700 font-extrabold px-3 py-1 text-xs rounded-full uppercase border border-green-200">
                        {{ $product->stock > 0 ? ($settings->enable_rtl ? 'دستیاب (In Stock)' : 'In Stock') : ($settings->enable_rtl ? 'آؤٹ آف اسٹاک' : 'Out of Stock') }}
                    </span>
                </div>

                <!-- Thumbnails Gallery -->
                @if(is_array($product->images) && count($product->images) > 1)
                    <div class="flex gap-2 overflow-x-auto py-2">
                        @foreach($product->images as $img)
                            <button type="button" onclick="document.getElementById('main-product-image').src = '{{ tenant_asset($img) }}'" class="w-20 h-20 rounded-xl overflow-hidden border border-gray-200 bg-white hover:border-green-600 transition shrink-0">
                                <img src="{{ tenant_asset($img) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Details Panel -->
            <div class="space-y-6">
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-2 leading-normal flex items-center gap-3">
                    {{ $product->name }}
                    @if($product->is_bundle)
                        <span class="bg-indigo-600 text-white font-extrabold text-[10px] px-2.5 py-1 rounded-full uppercase tracking-wider">Bundle Deal</span>
                    @endif
                </h1>
                
                <div class="flex items-center gap-2">
                    <a href="#reviews-section" class="flex items-center gap-2 hover:underline text-yellow-500 text-xl font-bold transition">
                        <span>★★★★★</span>
                        <span class="text-sm text-gray-500 font-bold">({{ $product->reviews->count() }} {{ $settings->enable_rtl ? 'کسٹمر ریویوز' : 'Customer Reviews' }})</span>
                    </a>
                </div>

                @if(!$product->is_bundle)
                <div class="flex items-center gap-4">
                    @if($product->is_discount)
                        <span class="text-4xl font-black text-green-600" id="main-selling-price">Rs. {{ number_format($product->final_price) }}</span>
                        <span class="text-xl font-bold text-gray-400 line-through" id="main-compare-price">Rs. {{ number_format((float)$product->price) }}</span>
                    @else
                        <span class="text-4xl font-black text-green-600" id="main-selling-price">Rs. {{ number_format((float)$product->price) }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-2xl font-bold text-gray-400 line-through" id="main-compare-price">Rs. {{ number_format((float)$product->compare_price) }}</span>
                        @else
                            <span class="text-2xl font-bold text-gray-400 line-through hidden" id="main-compare-price"></span>
                        @endif
                    @endif
                </div>
                @endif

                <!-- Product Variants Options selectors -->
                @if(!empty($product->variants) && is_array($product->variants))
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        @foreach($product->variants as $optionName => $optionValues)
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $optionName }}:</label>
                                <select data-option-name="{{ $optionName }}" class="variant-selector w-full px-4 py-3 border border-gray-250 rounded-xl bg-gray-50 focus:bg-white text-sm font-bold outline-none focus:border-green-600 transition">
                                    @foreach($optionValues as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Deal callout panels -->
                @if($product->is_bundle)
                    @if(!empty($product->bundle_options) && is_array($product->bundle_options))
                        <!-- Multi-option dynamic bundle deal selector -->
                        <div class="rounded-[2rem] overflow-hidden border-2 border-gray-200 shadow-xl mt-4" style="border-color: {{ $product->bundle_color_primary ?? '#16a34a' }};">
                            <!-- Premium Centered Header Banner -->
                            <div class="px-6 py-6 flex flex-col items-center justify-center text-center gap-2 font-bold" style="background-color: {{ $product->bundle_color_primary ?? '#16a34a' }}; color: {{ $product->bundle_color_text ?? '#ffffff' }};">
                                <span class="text-2xl md:text-3xl font-black tracking-wide flex items-center gap-2">🔥 {{ $product->bundle_header_title ?? ($settings->enable_rtl ? 'بڑی عید کی بڑی آفر' : 'Special Bundle Offer') }} 🔥</span>
                                @if($product->bundle_header_badge)
                                    <span class="bg-red-650 text-white text-xs px-3.5 py-1.5 rounded-full font-black tracking-wider animate-pulse uppercase shadow-sm mt-1" style="background-color: #dc2626;">
                                        {{ $product->bundle_header_badge }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Options list container -->
                            <div class="p-5 bg-white space-y-4">
                                @foreach($product->bundle_options as $index => $opt)
                                    @php
                                        $primaryColor = $product->bundle_color_primary ?? '#16a34a';
                                        $isSelected = ($index === 0);
                                        $borderStyle = $isSelected 
                                            ? "border-width: 4px; border-color: {$primaryColor}; background-color: {$primaryColor}10; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);" 
                                            : "border-width: 2px; border-color: #e2e8f0; background-color: #ffffff;";
                                    @endphp
                                    <div class="bundle-opt-label relative flex items-center justify-between p-3.5 md:p-5 rounded-[2rem] border cursor-pointer hover:border-emerald-400 transition duration-150" 
                                         style="{{ $borderStyle }}"
                                         onclick="selectBundleOption({{ $index }}, {{ $opt['price'] }}, {{ $opt['compare_price'] ?: 'null' }}, '{{ !empty($opt['image']) ? tenant_asset($opt['image']) : '' }}', '{{ addslashes($opt['title']) }}')">
                                        
                                        <!-- Top Right popularity Ribbon/Label -->
                                        @if(!empty($opt['label']))
                                            @php
                                                $ribbonBg = 'background-color: ' . $primaryColor . '; color: #ffffff;';
                                                $labelLower = mb_strtolower($opt['label']);
                                                if (
                                                    str_contains($labelLower, 'hot') || 
                                                    str_contains($labelLower, 'sale') || 
                                                    str_contains($labelLower, 'ہوٹ') || 
                                                    str_contains($labelLower, 'ہاٹ') || 
                                                    str_contains($labelLower, 'سیل')
                                                ) {
                                                    $ribbonBg = 'background-color: #ef4444; color: #ffffff;';
                                                }
                                            @endphp
                                            <span class="absolute -top-3.5 right-6 text-[10px] font-black px-3.5 py-1.5 rounded-lg uppercase tracking-wider shadow-sm" style="{{ $ribbonBg }}">
                                                {{ $opt['label'] }}
                                            </span>
                                        @endif
                                        
                                        <div class="flex items-center gap-3.5">
                                            <!-- Custom Radio Input styled like screenshot -->
                                            <div class="radio-wrapper w-6 h-6 md:w-7 md:h-7 rounded-full border-2 flex items-center justify-center shrink-0" style="border-color: {{ $isSelected ? $primaryColor : '#d1d5db' }};">
                                                <div class="radio-dot w-3 h-3 md:w-4 md:h-4 rounded-full {{ $isSelected ? 'block' : 'hidden' }}" style="background-color: {{ $primaryColor }};"></div>
                                            </div>

                                            <!-- Thumbnail image for bundle option -->
                                            @if(!empty($opt['image']))
                                                <img src="{{ tenant_asset($opt['image']) }}" class="w-12 h-12 md:w-14 md:h-14 rounded-2xl object-cover border border-gray-150 bg-white shrink-0">
                                            @endif
                                            
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm md:text-base font-black text-gray-900 leading-snug whitespace-normal md:whitespace-nowrap">
                                                    {!! preg_replace('/(\+.*(?:free|فری|مفت))/iu', '<span class="text-rose-600 font-black">$1</span>', e($opt['title'])) !!}
                                                </span>
                                                @if(!empty($opt['badge']))
                                                    <span class="text-white text-[10px] font-black px-2 py-0.5 rounded shadow-sm w-max" style="background-color: {{ $primaryColor }};">
                                                        {{ $opt['badge'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Prices on the right -->
                                        <div class="text-right flex flex-col justify-center shrink-0 font-sans pl-2">
                                            <span class="text-base md:text-lg font-black text-gray-900 whitespace-nowrap">Rs. {{ number_format($opt['price']) }}</span>
                                            @if(!empty($opt['compare_price']))
                                                <span class="text-xs md:text-sm font-extrabold text-red-650 line-through whitespace-nowrap" style="color: #dc2626;">Rs. {{ number_format($opt['compare_price']) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Fallback to old single bundle package style -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5 space-y-2.5">
                            <div class="flex items-center gap-2">
                                <span class="bg-indigo-600 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider">Bundle Package</span>
                                <h3 class="font-extrabold text-indigo-900 text-sm">{{ $product->bundle_title }}</h3>
                            </div>
                            @if($product->bundle_details)
                                <p class="text-xs text-indigo-700 font-medium leading-relaxed">
                                    <strong>{{ $settings->enable_rtl ? 'تفصیل:' : 'Details:' }}</strong> {{ $product->bundle_details }}
                                </p>
                            @endif
                            <div class="text-xs text-indigo-800 font-bold bg-white/60 inline-block px-3 py-1.5 rounded-lg border border-indigo-100 font-sans">
                                {{ $settings->enable_rtl ? 'بنڈل پرائس:' : 'Bundle Price:' }} <span class="text-indigo-600 font-black">Rs. {{ number_format((float)$product->bundle_price) }}</span>
                            </div>
                        </div>
                    @endif
                @endif

                @if($product->is_discount)
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 space-y-2.5">
                        <div class="flex items-center gap-2">
                            <span class="bg-rose-600 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $product->discount_badge ?? 'Special Discount' }}
                            </span>
                            <h3 class="font-extrabold text-rose-900 text-sm">{{ $settings->enable_rtl ? 'خصوصی ڈسکاؤنٹ ڈیل' : 'Special Discount Deal' }}</h3>
                        </div>
                        @if($product->discount_terms)
                            <p class="text-xs text-rose-700 font-medium leading-relaxed">
                                <strong>{{ $settings->enable_rtl ? 'شرائط:' : 'Terms:' }}</strong> {{ $product->discount_terms }}
                            </p>
                        @endif
                    </div>
                @endif

                <!-- Quantity & Add to Cart -->
                <div class="pt-3 space-y-4">
                    @if(!$product->is_bundle)
                        <div class="flex items-center gap-3">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $settings->enable_rtl ? 'تعداد' : 'Quantity' }}:</label>
                            <div class="flex items-center gap-2 bg-gray-50 p-1 rounded-xl border border-gray-250 font-sans">
                                <button type="button" onclick="const input = document.getElementById('qty_input'); if (input.value > 1) input.value--" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold hover:bg-gray-100 transition">-</button>
                                <input type="number" id="qty_input" value="1" min="1" class="w-12 text-center bg-transparent border-0 font-black text-sm outline-none">
                                <button type="button" onclick="const input = document.getElementById('qty_input'); input.value++" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded-lg text-sm font-bold hover:bg-gray-100 transition">+</button>
                            </div>
                        </div>
                    @else
                        <input type="hidden" id="qty_input" value="1">
                    @endif

                    <div class="flex flex-col gap-3.5 pt-2">
                        <!-- Out of stock status alert badge -->
                        <div id="variant-stock-alert" class="hidden bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl font-bold text-sm select-none">
                            <span>❌ {{ $settings->enable_rtl ? 'معذرت، منتخب کردہ ویرینٹ آؤٹ آف اسٹاک ہے۔' : 'Sorry, the selected variant is out of stock.' }}</span>
                        </div>

                        <!-- Add to Cart -->
                        <button id="btn-add-to-cart" onclick="triggerAddToCart(false)" style="background-color: {{ $settings->btn_add_to_cart_bg ?? '#16a34a' }}; color: {{ $settings->btn_add_to_cart_text_color ?? '#ffffff' }};" class="w-full font-black py-4 rounded-xl text-lg shadow-lg hover:opacity-90 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span id="btn-add-to-cart-text">{{ $settings->btn_add_to_cart_text ?? ($settings->enable_rtl ? 'کارٹ میں شامل کریں 🛒' : 'Add to Cart 🛒') }}</span>
                        </button>
                        <!-- Order Now - Cash on Delivery -->
                        <button id="btn-buy-now" onclick="triggerAddToCart(true)" style="background-color: {{ $settings->btn_buy_now_bg ?? '#84cc16' }}; color: {{ $settings->btn_buy_now_text_color ?? '#ffffff' }};" class="w-full font-black py-4 rounded-xl text-lg shadow-lg hover:opacity-90 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span id="btn-buy-now-text">{{ $settings->btn_buy_now_text ?? ($settings->enable_rtl ? 'ابھی آرڈر کریں - کیش آن ڈیلیوری 🚚' : 'Order Now - Cash on Delivery 🚚') }}</span>
                        </button>
                    </div>

                    <!-- Dynamic Delivery Timeline -->
                    @php
                        $orderedDate = date('M jS');
                        $readyStartDate = date('M jS');
                        $readyEndDate = date('M jS', strtotime('+1 day'));
                        $deliveredStartDate = date('M jS', strtotime('+1 day'));
                        $deliveredEndDate = date('M jS', strtotime('+3 days'));
                        $timelineColor = $settings->btn_buy_now_bg ?? '#84cc16';
                    @endphp
                    <div class="mt-8 pt-6 border-t border-gray-150">
                        <div class="flex items-center justify-between relative max-w-md mx-auto px-4">
                            <!-- Connecting Line -->
                            <div class="absolute left-10 right-10 top-6 h-1 bg-gray-200 -z-10">
                                <div class="h-full w-2/3" style="background-color: {{ $timelineColor }};"></div>
                            </div>
                            
                            <!-- Step 1: Ordered -->
                            <div class="flex flex-col items-center text-center space-y-1">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg shadow-md transition" style="background-color: {{ $timelineColor }};">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <span class="text-xs font-black text-gray-900">{{ $settings->enable_rtl ? 'آرڈر کیا' : 'Ordered' }}</span>
                                <span class="text-[10px] font-bold text-gray-500">{{ $orderedDate }}</span>
                            </div>
                            
                            <!-- Step 2: Order Ready -->
                            <div class="flex flex-col items-center text-center space-y-1">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg shadow-md transition" style="background-color: {{ $timelineColor }};">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                </div>
                                <span class="text-xs font-black text-gray-900">{{ $settings->enable_rtl ? 'تیار ہے' : 'Order Ready' }}</span>
                                <span class="text-[10px] font-bold text-gray-500">{{ $readyStartDate }} - {{ $readyEndDate }}</span>
                            </div>
                            
                            <!-- Step 3: Delivered -->
                            <div class="flex flex-col items-center text-center space-y-1">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg shadow-md transition" style="background-color: {{ $timelineColor }}; opacity: 0.85;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10l8 4"></path></svg>
                                </div>
                                <span class="text-xs font-black text-gray-900">{{ $settings->enable_rtl ? 'ڈلیور ہو گا' : 'Delivered' }}</span>
                                <span class="text-[10px] font-bold text-gray-500">{{ $deliveredStartDate }} - {{ $deliveredEndDate }}</span>
                            </div>
                        </div>
                    </div>

                <!-- Product Description (Positioned below Add to Cart / Buy Now buttons) -->
                @if($product->description)
                    <div class="pt-6 border-t border-gray-100 space-y-2">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $settings->enable_rtl ? 'مصنوعات کی تفصیل' : 'Product Description' }}</h4>
                        <div class="prose max-w-none text-slate-700 font-medium">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

        <!-- Customer Reviews Panel -->
        <div id="reviews-section" class="max-w-4xl mx-auto py-16 border-t border-gray-100 mt-16 space-y-12">
            <h2 class="text-3xl font-black text-gray-900 text-center">{{ $settings->enable_rtl ? 'خریداروں کی رائے' : 'Customer Reviews' }}</h2>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl font-bold text-center">✅ {{ session('success') }}</div>
            @endif

            @php
                $totalReviews = $product->reviews->count();
                $avgRating = $totalReviews > 0 ? $product->reviews->avg('rating') : 5.0;
                $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                foreach($product->reviews as $r) {
                    $ratingCounts[(int)$r->rating] = ($ratingCounts[(int)$r->rating] ?? 0) + 1;
                }
            @endphp

            <!-- Customer Reviews Summary Grid -->
            <div class="bg-white border border-gray-100 rounded-[2rem] p-8 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                <!-- Left Column: Avg Rating -->
                <div class="flex flex-col items-center justify-center text-center border-r border-gray-100 last:border-r-0 pb-6 md:pb-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[#16a34a] text-2xl flex">
                            @php
                                $fullStars = floor($avgRating);
                            @endphp
                            @for($i=1; $i<=5; $i++)
                                @if($i <= $fullStars)
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                        </span>
                        <span class="text-xl font-black text-gray-900">{{ number_format($avgRating, 2) }} {{ $settings->enable_rtl ? 'میں سے 5' : 'out of 5' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-bold justify-center">
                        <span>{{ $settings->enable_rtl ? "{$totalReviews} ریویوز کی بنیاد پر" : "Based on {$totalReviews} reviews" }}</span>
                        <span class="text-green-600 font-sans text-sm">✔</span>
                    </div>
                </div>
                
                <!-- Middle Column: Histogram Bars -->
                <div class="flex flex-col gap-2.5 px-0 md:px-6">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $count = $ratingCounts[$star] ?? 0;
                            $pct = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                        @endphp
                        <div class="flex items-center gap-3 text-sm font-bold text-gray-600">
                            <div class="text-[#16a34a] flex shrink-0 tracking-tighter">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $star)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <div class="flex-grow h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#16a34a] rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0 min-w-[12px] text-right">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Right Column: Write a Review Button -->
                <div class="flex justify-center md:justify-end">
                    <button onclick="document.getElementById('write-review-form').classList.toggle('hidden')" class="btn-secondary-custom font-black py-4 px-10 rounded-xl transition shadow-lg hover:-translate-y-0.5 w-full md:w-auto text-center text-sm uppercase tracking-wider">
                        {{ $settings->enable_rtl ? 'ریویو لکھیں' : 'Write a review' }}
                    </button>
                </div>
            </div>

            <!-- Add Review Form (Hidden initially) -->
            <div id="write-review-form" class="hidden bg-white border rounded-[2rem] p-8 shadow-sm border-gray-100 mt-4">
                <h3 class="text-xl font-black mb-4 text-gray-800">{{ $settings->enable_rtl ? 'اپنا قیمتی ریویو لکھیں:' : 'Write your review:' }}</h3>
                <form action="/product/{{ $product->id }}/review" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">{{ $settings->enable_rtl ? 'آپ کا نام' : 'Your Name' }}</label>
                            <input type="text" name="customer_name" required placeholder="{{ $settings->enable_rtl ? 'مثال: عاصم خان' : 'e.g. Asim Khan' }}" class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white text-sm outline-none focus:border-green-600 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">{{ $settings->enable_rtl ? 'سٹارز' : 'Rating' }}</label>
                            <select name="rating" class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white font-bold text-sm outline-none focus:border-green-600 transition">
                                <option value="5">★★★★★ (5/5)</option>
                                <option value="4">★★★★☆ (4/5)</option>
                                <option value="3">★★★☆☆ (3/5)</option>
                                <option value="2">★★☆☆☆ (2/5)</option>
                                <option value="1">★☆☆☆☆ (1/5)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-600 mb-1">{{ $settings->enable_rtl ? 'آپ کے تاثرات / کمنٹ' : 'Comment' }}</label>
                        <textarea name="comment" rows="3" required placeholder="{{ $settings->enable_rtl ? 'ریویو کی تفصیل لکھیں...' : 'Write comment details...' }}" class="w-full px-4 py-3 border rounded-xl bg-gray-50 focus:bg-white text-sm outline-none focus:border-green-600 transition leading-normal"></textarea>
                    </div>
                    <button type="submit" class="btn-primary-custom font-bold py-3.5 px-8 rounded-xl transition text-sm shadow-md">
                        {{ $settings->enable_rtl ? 'ریویو سبمٹ کریں ✨' : 'Submit Review ✨' }}
                    </button>
                </form>
            </div>

            <!-- Sort Form (Header visual) -->
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <div class="relative inline-block text-left">
                    <span class="text-sm font-black text-[#16a34a] cursor-pointer flex items-center gap-1.5 hover:text-green-700 transition">
                        {{ $settings->enable_rtl ? 'تازہ ترین' : 'Most Recent' }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </div>
            </div>

            <!-- Reviews list -->
            <div class="space-y-8 mt-6">
                @forelse($product->reviews as $review)
                    <div class="border-b border-gray-100 pb-8 last:border-b-0">
                        <!-- Top Row: Stars and Date -->
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-[#16a34a] flex text-sm">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs font-semibold text-gray-400 font-sans" style="font-family: sans-serif;">
                                {{ $review->created_at ? $review->created_at->format('d/m/Y') : now()->format('d/m/Y') }}
                            </span>
                        </div>

                        <!-- Middle Row: User Avatar and Username -->
                        <div class="flex items-center gap-2 mb-3">
                            <div class="bg-gray-50 border border-gray-200 text-gray-400 w-8 h-8 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <h4 class="font-black text-[#16a34a] text-sm md:text-base leading-normal">
                                {{ $review->customer_name }}
                            </h4>
                        </div>

                        <!-- Bottom Row: Testimonial text -->
                        <p class="text-gray-700 text-sm md:text-base leading-relaxed font-medium">
                            {{ $review->comment }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-gray-400 italic py-10 font-bold border-2 border-dashed border-gray-100 bg-gray-50 rounded-2xl w-full">
                        {{ $settings->enable_rtl ? 'اس پروڈکٹ کا ابھی کوئی ریویو نہیں ہے۔ پہلے خریدار آپ بنیں!' : 'No reviews uploaded yet. Be the first to review!' }}
                    </p>
                @endforelse
            </div>
        </div>
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

        let activePrice = {{ !empty($product->bundle_options) && is_array($product->bundle_options) ? $product->bundle_options[0]['price'] : $product->final_price }};
        let activeOriginalPrice = {{ !empty($product->bundle_options) && is_array($product->bundle_options) ? ($product->bundle_options[0]['compare_price'] ?: $product->bundle_options[0]['price']) : ($product->is_bundle || $product->is_discount ? $product->price : ($product->compare_price ?: $product->price)) }};
        let activeName = '{{ addslashes($product->name) }}';
        let defaultProductImage = document.getElementById('main-product-image') ? document.getElementById('main-product-image').src : '';

        const variantCombinations = {!! json_encode($product->variant_combinations ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

        function updateSelectedVariantPriceAndStock() {
            if (!variantCombinations || variantCombinations.length === 0) return;

            const selection = {};
            document.querySelectorAll('.variant-selector').forEach(select => {
                selection[select.getAttribute('data-option-name')] = select.value;
            });

            // Find matching combination
            const match = variantCombinations.find(item => {
                if (!item.combination) return false;
                for (let optName in item.combination) {
                    if (item.combination[optName] !== selection[optName]) {
                        return false;
                    }
                }
                return true;
            });

            const sellingPriceEl = document.getElementById('main-selling-price');
            const comparePriceEl = document.getElementById('main-compare-price');
            const stockAlertEl = document.getElementById('variant-stock-alert');
            const btnAddToCart = document.getElementById('btn-add-to-cart');
            const btnBuyNow = document.getElementById('btn-buy-now');

            if (match) {
                // Update prices
                if (match.price !== undefined && match.price !== null && match.price !== '') {
                    activePrice = parseFloat(match.price);
                } else {
                    activePrice = parseFloat("{{ $product->final_price }}");
                }

                if (match.compare_price !== undefined && match.compare_price !== null && match.compare_price !== '') {
                    activeOriginalPrice = parseFloat(match.compare_price);
                } else {
                    activeOriginalPrice = parseFloat("{{ $product->compare_price ?: $product->price }}");
                }

                // Update stock/button state
                const isOutOfStock = (match.stock !== undefined && match.stock !== null && parseInt(match.stock) <= 0);
                if (isOutOfStock) {
                    if (stockAlertEl) stockAlertEl.classList.remove('hidden');
                    if (btnAddToCart) {
                        btnAddToCart.disabled = true;
                        btnAddToCart.classList.add('opacity-50', 'pointer-events-none');
                    }
                    if (btnBuyNow) {
                        btnBuyNow.disabled = true;
                        btnBuyNow.classList.add('opacity-50', 'pointer-events-none');
                    }
                } else {
                    if (stockAlertEl) stockAlertEl.classList.add('hidden');
                    if (btnAddToCart) {
                        btnAddToCart.disabled = false;
                        btnAddToCart.classList.remove('opacity-50', 'pointer-events-none');
                    }
                    if (btnBuyNow) {
                        btnBuyNow.disabled = false;
                        btnBuyNow.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }
            } else {
                // Fallback to defaults
                activePrice = parseFloat("{{ $product->final_price }}");
                activeOriginalPrice = parseFloat("{{ $product->compare_price ?: $product->price }}");

                // Check default product stock
                const defaultStock = parseInt("{{ $product->stock ?? 10 }}");
                if (defaultStock <= 0) {
                    if (stockAlertEl) stockAlertEl.classList.remove('hidden');
                    if (btnAddToCart) {
                        btnAddToCart.disabled = true;
                        btnAddToCart.classList.add('opacity-50', 'pointer-events-none');
                    }
                    if (btnBuyNow) {
                        btnBuyNow.disabled = true;
                        btnBuyNow.classList.add('opacity-50', 'pointer-events-none');
                    }
                } else {
                    if (stockAlertEl) stockAlertEl.classList.add('hidden');
                    if (btnAddToCart) {
                        btnAddToCart.disabled = false;
                        btnAddToCart.classList.remove('opacity-50', 'pointer-events-none');
                    }
                    if (btnBuyNow) {
                        btnBuyNow.disabled = false;
                        btnBuyNow.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }
            }

            // Update displayed prices
            if (sellingPriceEl) {
                sellingPriceEl.innerText = 'Rs. ' + activePrice.toLocaleString();
            }
            if (comparePriceEl) {
                if (activeOriginalPrice > activePrice) {
                    comparePriceEl.innerText = 'Rs. ' + activeOriginalPrice.toLocaleString();
                    comparePriceEl.classList.remove('hidden');
                } else {
                    comparePriceEl.classList.add('hidden');
                }
            }
        }

        // Attach event listeners
        document.querySelectorAll('.variant-selector').forEach(select => {
            select.addEventListener('change', updateSelectedVariantPriceAndStock);
        });

        // Initialize state
        updateSelectedVariantPriceAndStock();

        function selectBundleOption(index, price, comparePrice, imagePath, optionTitle) {
            activePrice = parseFloat(price);
            activeOriginalPrice = parseFloat(comparePrice || price);
            activeName = '{{ addslashes($product->name) }} - ' + optionTitle;

            // Update displayed prices
            const sellingPriceEl = document.getElementById('main-selling-price');
            const comparePriceEl = document.getElementById('main-compare-price');
            if (sellingPriceEl) {
                sellingPriceEl.innerText = 'Rs. ' + activePrice.toLocaleString();
            }
            if (comparePriceEl) {
                if (comparePrice) {
                    comparePriceEl.innerText = 'Rs. ' + activeOriginalPrice.toLocaleString();
                    comparePriceEl.classList.remove('hidden');
                } else {
                    comparePriceEl.classList.add('hidden');
                }
            }

            // Update main product image if bundle option has image
            const mainImg = document.getElementById('main-product-image');
            if (mainImg) {
                if (imagePath && imagePath.trim() !== '') {
                    mainImg.src = imagePath;
                } else {
                    mainImg.src = defaultProductImage;
                }
            }

            // Update active state styling for options list using dynamic colors
            const labels = document.querySelectorAll('.bundle-opt-label');
            labels.forEach((label, idx) => {
                const dot = label.querySelector('.radio-dot');
                const radioWrapper = label.querySelector('.radio-wrapper');
                if (idx === index) {
                    label.style.borderWidth = '4px';
                    label.style.borderColor = '{{ $product->bundle_color_primary ?? "#16a34a" }}';
                    label.style.backgroundColor = '{{ $product->bundle_color_primary ?? "#16a34a" }}10';
                    label.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
                    if (dot) {
                        dot.classList.remove('hidden');
                        dot.style.backgroundColor = '{{ $product->bundle_color_primary ?? "#16a34a" }}';
                    }
                    if (radioWrapper) {
                        radioWrapper.style.borderColor = '{{ $product->bundle_color_primary ?? "#16a34a" }}';
                    }
                } else {
                    label.style.borderWidth = '2px';
                    label.style.borderColor = '#e2e8f0'; // border-gray-250
                    label.style.backgroundColor = '#ffffff';
                    label.style.boxShadow = 'none';
                    if (dot) dot.classList.add('hidden');
                    if (radioWrapper) {
                        radioWrapper.style.borderColor = '#d1d5db'; // border-gray-300
                    }
                }
            });
        }

        function triggerAddToCart(redirect = false) {
            const qty = document.getElementById('qty_input').value;
            const selectedVariants = {};
            
            document.querySelectorAll('.variant-selector').forEach(select => {
                const optionName = select.getAttribute('data-option-name');
                selectedVariants[optionName] = select.value;
            });
            
            addToCart(
                {{ $product->id }}, 
                activeName, 
                activePrice, 
                document.getElementById('main-product-image') ? document.getElementById('main-product-image').getAttribute('src') : '', 
                qty, 
                redirect, 
                selectedVariants,
                activeOriginalPrice
            );
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