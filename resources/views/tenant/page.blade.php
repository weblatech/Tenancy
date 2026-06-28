<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page->title }} - {{ strtoupper($tenantId) }}</title>
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
                                $rawUrl = $menuItem['url'] ?? '/';
                                $url = $rawUrl;
                                if (str_starts_with($rawUrl, '/')) {
                                    $url = tenant_store_url($rawUrl);
                                }
                                $isActive = request()->is(trim($rawUrl, '/')) || request()->is(trim($rawUrl, '/') . '/*');
                            @endphp
                            <a href="{{ $url }}" class="header-menu-link text-base font-bold {{ $isActive ? 'active' : '' }}">{{ $menuItem['label'] }}</a>
                        @endforeach
                    @else
                        <a href="{{ tenant_store_url('/') }}" class="header-menu-link text-base font-bold {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        <a href="{{ tenant_store_url('/collection') }}" class="header-menu-link text-base font-bold {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
                    @endif
                </nav>
                <div class="flex items-center space-x-4">
                    <!-- Account Icon -->
                    <a href="{{ auth('customer')->check() ? tenant_store_url('/customer/dashboard') : tenant_store_url('/customer/login') }}" 
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
                        $rawUrl = $menuItem['url'] ?? '/';
                        $url = $rawUrl;
                        if (str_starts_with($rawUrl, '/')) {
                            $url = tenant_store_url($rawUrl);
                        }
                        $isActive = request()->is(trim($rawUrl, '/')) || request()->is(trim($rawUrl, '/') . '/*');
                    @endphp
                    <a href="{{ $url }}" class="header-menu-link block font-bold py-2 {{ $isActive ? 'active' : '' }}">{{ $menuItem['label'] }}</a>
                @endforeach
            @else
                <a href="{{ tenant_store_url('/') }}" class="header-menu-link block font-bold py-2 {{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="{{ tenant_store_url('/collection') }}" class="header-menu-link block font-bold py-2 {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
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

    <!-- Content Container -->
    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl p-8 md:p-14">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-8 pb-4 border-b border-gray-100 leading-normal">{{ $page->title }}</h1>
            <div class="prose max-w-none text-gray-700 leading-relaxed text-base {{ (str_contains($page->content, '<div') || str_contains($page->content, '<form') || str_contains($page->content, '<p') || str_contains($page->content, '<section')) ? '' : 'whitespace-pre-line' }} font-medium">
                {!! $page->content !!}
            </div>
        </div>
    </main>

    @include('tenant.partials.footer')

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

                    fetch('{{ tenant_store_url("/newsletter-subscribe") }}', {
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
