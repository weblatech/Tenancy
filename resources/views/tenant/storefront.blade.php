<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ tenant('name') ?? strtoupper($tenantId) }} - Official Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    @if(!empty($settings->facebook_pixel_id))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings->facebook_pixel_id }}');
        fbq('track', 'PageView');
    </script>
    @endif

    @if($settings->meta_pixel_active && !empty($settings->meta_pixel_id))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings->meta_pixel_id }}');
        fbq('track', 'PageView');
    </script>
    @endif

    @if($settings->google_analytics_id && !empty($settings->google_analytics_id))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->google_analytics_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $settings->google_analytics_id }}');
    </script>
    @endif

    @if($settings->google_tag_manager_id && !empty($settings->google_tag_manager_id))
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $settings->google_tag_manager_id }}');</script>
    @endif

    @if($settings->tiktok_pixel_active && !empty($settings->tiktok_pixel_id))
    <script>
        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e+""]=+new Date,ttq._o=ttq._o||{},ttq._o[e+""]=n||{};var a=document.createElement("script");a.type="text/javascript",a.async=!0,a.src=r+"?sdkid="+e+"&lib="+t;var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(a,s)};
            ttq.load('{{ $settings->tiktok_pixel_id }}');
            ttq.page();
        }(window, document, 'ttq');
    </script>
    @endif

    @if($settings->snapchat_pixel_active && !empty($settings->snapchat_pixel_id))
    <script>
        (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};e.snaptr=a;a.push=a;a.loaded=!0;a.version='1.0';a.queue=[];var s=t.createElement(n);s.async=!0;s.src='https://sc-static.net/scevent.min.js';var r=t.getElementsByTagName(n)[0];r.parentNode.insertBefore(s,r)})(window,document,'script');
        snaptr('init', '{{ $settings->snapchat_pixel_id }}');
        snaptr('track', 'PAGE_VIEW');
    </script>
    @endif

    @if($settings->pinterest_tag_active && !empty($settings->pinterest_tag_id))
    <script>
        !function(e){if(!window.pintr){window.pintr=function(){window.pintr.queue||([]).push(Array.prototype.slice.call(arguments))}};var t=document.createElement("script");t.async=!0,t.src="https://assets.pinterest.com/js/pinit.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n)}();
        pintr('load', '{{ $settings->pinterest_tag_id }}');
        pintr('track', 'pagevisit');
    </script>
    @endif

    @if($settings->twitter_pixel_active && !empty($settings->twitter_pixel_id))
    <script>
        !function(e,t,n,s,u,i,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);},s.version='1.1',s.queue=[],i=t.createElement(n),i.async=!0,i.src='https://static.ads-twitter.com/uwt.js',a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(i,a))}(window,document,'script');
        twq('init','{{ $settings->twitter_pixel_id }}');
        twq('track','PageView');
    </script>
    @endif

    @if($settings->custom_tracking_active && !empty($settings->custom_tracking_head))
    {!! $settings->custom_tracking_head !!}
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
                            <span class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight" style="color: {{ $settings->header_menu_text ?? '#1f2937' }};">🛍️ {{ tenant('name') ?? strtoupper($tenantId) }}</span>
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

    @if($settings && $settings->hero_layout_type == 'none')
        <!-- Hero banner is disabled -->
    @elseif($settings && $settings->hero_layout_type == 'custom_code')
        {!! $settings->hero_custom_code !!}
    @elseif($settings && $settings->hero_layout_type == 'image' && ($settings->hero_image || !empty($settings->hero_images)))
        @php
            $heroImages = $settings->hero_images ?? ($settings->hero_image ? [$settings->hero_image] : []);
            
            $hasHeroText = !empty($settings->hero_title) || !empty($settings->hero_subtitle) || !empty($settings->hero_btn_text) || !empty($settings->hero_btn2_text);

            $heightClass = 'py-32 md:py-40 min-h-[300px] md:min-h-[450px]';
            if (($settings->hero_height ?? '') == 'small') $heightClass = 'py-20 md:py-24 min-h-[220px] md:min-h-[350px]';
            elseif (($settings->hero_height ?? '') == 'large') $heightClass = 'py-48 md:py-60 min-h-[400px] md:min-h-[600px]';

            $alignClass = 'text-center items-center justify-center';
            $textContainerAlign = 'mx-auto';
            if (($settings->hero_align ?? '') == 'left') {
                $alignClass = 'text-left items-start justify-start';
                $textContainerAlign = 'mr-auto md:ml-0';
            } elseif (($settings->hero_align ?? '') == 'right') {
                $alignClass = 'text-right items-end justify-end';
                $textContainerAlign = 'ml-auto md:mr-0';
            }
            
            $overlayOpacity = ($settings->hero_overlay_opacity ?? 50) / 100;
        @endphp
        
        @if(!$hasHeroText)
            <!-- Clean Image Banner / Slider Mode (No Text / No Overlays / Aspect Ratio fully visible and responsive) -->
            <div class="w-full relative group">
                @if(count($heroImages) > 1)
                    <!-- Multi-Image Swiper Slider (Responsive height) -->
                    <div class="swiper hero-slider w-full">
                        <div class="swiper-wrapper">
                            @foreach($heroImages as $img)
                                <div class="swiper-slide w-full">
                                    <img src="{{ tenant_asset($img) }}" class="w-full h-auto object-contain block" alt="Store Banner">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next !text-white drop-shadow-md opacity-0 group-hover:opacity-100 transition duration-300"></div>
                        <div class="swiper-button-prev !text-white drop-shadow-md opacity-0 group-hover:opacity-100 transition duration-300"></div>
                        <div class="swiper-pagination !bullet-white"></div>
                    </div>
                @elseif(count($heroImages) == 1)
                    <!-- Single Image (Full Width, Natural Aspect Ratio, No Cropping on mobile/desktop) -->
                    <img src="{{ tenant_asset($heroImages[0]) }}" class="w-full h-auto object-contain block" alt="Store Banner">
                @endif
            </div>
        @else
            <!-- Overlay Mode (Text / Buttons are overlayed over background images) -->
            <div class="w-full relative">
                @if(count($heroImages) > 1)
                    <div class="swiper hero-slider w-full h-full absolute inset-0">
                        <div class="swiper-wrapper h-full">
                            @foreach($heroImages as $img)
                                <div class="swiper-slide w-full h-full bg-cover bg-center" style="background-image: url('{{ tenant_asset($img) }}'); font-size: 0px;"></div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ tenant_asset($heroImages[0]) }}');"></div>
                @endif
                
                <!-- Overlay and Text Container -->
                <div class="relative w-full z-10 flex {{ $alignClass }} px-4 sm:px-6 lg:px-8 {{ $heightClass }}">
                    <div class="absolute inset-0 bg-black pointer-events-none" style="opacity: {{ $overlayOpacity }}; border-radius: 0px;"></div>
                    <div class="relative z-10 w-full max-w-7xl flex {{ $alignClass }}">
                        @if($settings->hero_show_container)
                            <div class="bg-black/45 backdrop-blur-lg border border-white/10 shadow-2xl rounded-[2rem] p-8 md:p-12 text-white max-w-3xl {{ $textContainerAlign }}">
                                @if(!empty($settings->hero_title))
                                    <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">{{ $settings->hero_title }}</h2>
                                @endif
                                @if(!empty($settings->hero_subtitle))
                                    <p class="text-lg md:text-xl mb-10 opacity-90 leading-relaxed">{{ $settings->hero_subtitle }}</p>
                                @endif
                                <div class="flex flex-wrap gap-4 justify-center {{ ($settings->hero_align ?? '') == 'center' ? 'justify-center' : (($settings->hero_align ?? '') == 'right' ? 'justify-end' : 'justify-start') }}">
                                    @if(!empty($settings->hero_btn_text))
                                        <a href="{{ $settings->hero_btn_link ?? '#products' }}" class="btn-primary-custom font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base">
                                            {{ $settings->hero_btn_text }}
                                        </a>
                                    @endif
                                    @if(!empty($settings->hero_btn2_text))
                                        <a href="{{ $settings->hero_btn2_link ?? '#products' }}" class="btn-secondary-custom font-black py-4 px-10 rounded-xl transition inline-block text-base border border-white">
                                            {{ $settings->hero_btn2_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-white max-w-3xl {{ $textContainerAlign }}">
                                @if(!empty($settings->hero_title))
                                    <h2 class="text-5xl md:text-6xl font-black mb-6 drop-shadow-lg leading-tight">{{ $settings->hero_title }}</h2>
                                @endif
                                @if(!empty($settings->hero_subtitle))
                                    <p class="text-xl md:text-2xl mb-12 opacity-95 drop-shadow-md leading-relaxed">{{ $settings->hero_subtitle }}</p>
                                @endif
                                <div class="flex flex-wrap gap-4 justify-center {{ ($settings->hero_align ?? '') == 'center' ? 'justify-center' : (($settings->hero_align ?? '') == 'right' ? 'justify-end' : 'justify-start') }}">
                                    @if(!empty($settings->hero_btn_text))
                                        <a href="{{ $settings->hero_btn_link ?? '#products' }}" class="btn-primary-custom font-black py-4 px-12 rounded-full shadow-xl transition transform hover:scale-105 inline-block text-base">
                                            {{ $settings->hero_btn_text }}
                                        </a>
                                    @endif
                                    @if(!empty($settings->hero_btn2_text))
                                        <a href="{{ $settings->hero_btn2_link ?? '#products' }}" class="btn-secondary-custom font-black py-4 px-12 rounded-full transition inline-block text-base border border-white">
                                            {{ $settings->hero_btn2_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        @php
            $heightClass = 'py-24 md:py-32';
            if (($settings->hero_height ?? '') == 'small') $heightClass = 'py-16 md:py-20';
            elseif (($settings->hero_height ?? '') == 'large') $heightClass = 'py-32 md:py-48';

            $alignClass = 'text-center items-center justify-center';
            $textContainerAlign = 'mx-auto';
            if (($settings->hero_align ?? '') == 'left') {
                $alignClass = 'text-left items-start justify-start';
                $textContainerAlign = 'mr-auto md:ml-0';
            } elseif (($settings->hero_align ?? '') == 'right') {
                $alignClass = 'text-right items-end justify-end';
                $textContainerAlign = 'ml-auto md:mr-0';
            }
        @endphp
        <div class="{{ $heightClass }} border-b border-gray-200 px-4 flex {{ $alignClass }}" style="background-color: {{ $settings->hero_bg_color ?? '#eff6ff' }}; color: {{ $settings->hero_text_color ?? '#1e3a8a' }};">
            <div class="w-full max-w-7xl flex {{ $alignClass }}">
                @if($settings->hero_show_container)
                    <div class="bg-white/70 backdrop-blur-md shadow-xl border border-gray-150 rounded-[2rem] p-8 md:p-12 max-w-3xl {{ $textContainerAlign }}" style="color: {{ $settings->hero_text_color ?? '#1e3a8a' }}">
                        <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">{{ $settings->hero_title ?? 'Welcome to Our Store!' }}</h2>
                        <p class="text-lg md:text-xl mb-10 opacity-90 leading-relaxed">{{ $settings->hero_subtitle ?? 'Discover our premium collection of herbal wellness products.' }}</p>
                        <div class="flex flex-wrap gap-4 justify-center {{ ($settings->hero_align ?? '') == 'center' ? 'justify-center' : (($settings->hero_align ?? '') == 'right' ? 'justify-end' : 'justify-start') }}">
                            @if(!empty($settings->hero_btn_text))
                                <a href="{{ $settings->hero_btn_link ?? '#products' }}" class="btn-primary-custom font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base">
                                    {{ $settings->hero_btn_text }}
                                </a>
                            @endif
                            @if(!empty($settings->hero_btn2_text))
                                <a href="{{ $settings->hero_btn2_link ?? '#products' }}" class="font-black py-4 px-10 rounded-xl border-2 transition inline-block text-base bg-transparent hover:bg-black/5" style="border-color: {{ $settings->hero_text_color ?? '#1e3a8a' }};">
                                    {{ $settings->hero_btn2_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="max-w-3xl {{ $textContainerAlign }}">
                        <h2 class="text-5xl md:text-6xl font-black mb-6 leading-tight">{{ $settings->hero_title ?? 'Welcome to Our Store!' }}</h2>
                        <p class="text-xl md:text-2xl opacity-90 mb-10 leading-relaxed">{{ $settings->hero_subtitle ?? 'Discover our premium collection of herbal wellness products.' }}</p>
                        <div class="flex flex-wrap gap-4 justify-center {{ ($settings->hero_align ?? '') == 'center' ? 'justify-center' : (($settings->hero_align ?? '') == 'right' ? 'justify-end' : 'justify-start') }}">
                            @if(!empty($settings->hero_btn_text))
                                <a href="{{ $settings->hero_btn_link ?? '#products' }}" class="font-black py-4 px-12 rounded-full shadow-xl transition transform hover:scale-105 inline-block text-base" style="background-color: {{ $settings->hero_text_color ?? '#1e3a8a' }}; color: {{ $settings->hero_bg_color ?? '#eff6ff' }};">
                                    {{ $settings->hero_btn_text }}
                                </a>
                            @endif
                            @if(!empty($settings->hero_btn2_text))
                                <a href="{{ $settings->hero_btn2_link ?? '#products' }}" class="font-black py-4 px-12 rounded-full border-2 transition inline-block text-base bg-transparent hover:bg-black/5" style="border-color: {{ $settings->hero_text_color ?? '#1e3a8a' }};">
                                    {{ $settings->hero_btn2_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- 🧩 Dynamic Sections Loop (With Spacing Support) -->
    @foreach($sections as $section)
        @php 
            $s = $section->settings; 
            // Default Spacing classes if not set
            $pt = $s['pt'] ?? 'pt-10 md:pt-12';
            $pb = $s['pb'] ?? 'pb-10 md:pb-12';
        @endphp

        @if($section->type == 'custom_code')
            <section class="store-custom-section {{ $pt }} {{ $pb }}">{!! $section->content !!}</section>
        
        @elseif($section->type == 'discount_banner')
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <div class="bg-gradient-to-br from-red-600 to-rose-500 text-white py-16 px-6 md:px-12 rounded-[2rem] shadow-2xl text-center relative overflow-hidden">
                    @if(!empty($s['badge'])) <span class="inline-block bg-yellow-400 text-yellow-900 font-black px-4 py-1 rounded-full text-sm mb-6 shadow-md">{{ $s['badge'] }}</span> @endif
                    <h2 class="text-4xl md:text-5xl font-black mb-6">{{ $s['heading'] ?? '' }} <span class="text-yellow-300">{{ $s['highlight'] ?? '' }}</span></h2>
                    <p class="text-xl md:text-2xl mb-10 opacity-95">{{ $s['description'] ?? '' }}</p>
                    @if(!empty($s['btn_text']))
                        <a href="#products" class="inline-block bg-white text-red-600 font-black py-4 px-10 rounded-full shadow-lg hover:bg-gray-50 transition transform hover:scale-105">{{ $s['btn_text'] }}</a>
                    @endif
                </div>
            </section>

        @elseif($section->type == 'image_with_text')
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <div class="flex flex-col {{ (isset($s['layout']) && $s['layout'] == 'image_right') ? 'md:flex-row-reverse' : 'md:flex-row' }} items-center gap-16">
                    <div class="w-full md:w-1/2">
                        @if(!empty($s['image']))
                            <img src="{{ tenant_asset($s['image']) }}" alt="Section Image" class="w-full rounded-3xl shadow-2xl object-cover hover:shadow-green-500/20 transition duration-500">
                        @else
                            <div class="w-full h-96 bg-gray-100 rounded-3xl flex items-center justify-center text-gray-400 font-bold border-2 border-dashed border-gray-200">Image Placeholder</div>
                        @endif
                    </div>
                    <div class="w-full md:w-1/2 text-center md:text-left">
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 leading-tight">{{ $s['heading'] ?? '' }}</h2>
                        <p class="text-lg md:text-xl text-gray-600 mb-10 leading-relaxed">{{ $s['text'] ?? '' }}</p>
                        @if(!empty($s['btn_text']))
                            <a href="{{ $s['btn_link'] ?? '#' }}" class="btn-primary-custom font-black py-4 px-12 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                                {{ $s['btn_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </section>

        <!-- ⭐ Premium Features Bar / Trust Badges (No empty boxes, huge beautiful icons) -->
        @elseif($section->type == 'features_bar')
            <section class="border-y border-gray-100 bg-white {{ $pt }} {{ $pb }}">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        @foreach([1, 2, 3, 4] as $i)
                            @if(!empty($s["f{$i}"]) || !empty($s["f{$i}_icon"]))
                            <div class="flex flex-col items-center justify-start p-6 bg-gray-50 hover:bg-white rounded-3xl hover:shadow-2xl transition-all duration-300 border border-transparent hover:border-green-100 group">
                                @if(!empty($s["f{$i}_icon"]))
                                    <img src="{{ tenant_asset($s["f{$i}_icon"]) }}" class="h-28 w-28 md:h-36 md:w-36 mb-6 object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-300" alt="{{ $s["f{$i}"] }}">
                                @else
                                    <div class="w-24 h-24 md:w-32 md:h-32 bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-6 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-12 h-12 md:w-16 md:h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @endif
                                @if(!empty($s["f{$i}"]))
                                    <span class="font-black text-gray-900 text-lg md:text-xl">{{ $s["f{$i}"] }}</span>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

        <!-- ⭐ Testimonials Slider -->
        @elseif($section->type == 'testimonials')
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-hidden {{ $pt }} {{ $pb }}">
                <h2 class="text-4xl font-black text-center text-gray-900 mb-8 md:mb-10">{{ $s['heading'] ?? 'Customer Reviews' }}</h2>
                <div class="swiper testimonials-slider px-2 pb-14" dir="rtl">
                    <div class="swiper-wrapper">
                        @foreach([['n'=>'r1_name','t'=>'r1_text'], ['n'=>'r2_name','t'=>'r2_text'], ['n'=>'r3_name','t'=>'r3_text']] as $r)
                            @if(!empty($s[$r['n']]))
                            <div class="swiper-slide h-auto">
                                <div class="bg-gray-50 rounded-[2rem] p-10 relative pt-14 shadow-sm border border-gray-100 mx-2 mt-6 h-full flex flex-col justify-between hover:shadow-xl transition duration-300">
                                    <div class="absolute -top-6 right-8 bg-green-600 text-white w-14 h-14 flex items-center justify-center rounded-full text-4xl font-serif leading-none shadow-lg">"</div>
                                    <div>
                                        <div class="text-green-500 flex justify-center mb-6 text-2xl tracking-widest">★★★★★</div>
                                        <p class="text-center text-gray-700 mb-8 leading-relaxed font-bold text-lg">"{{ $s[$r['t']] }}"</p>
                                    </div>
                                    <div class="border-t border-gray-200 pt-5 text-center mt-auto">
                                        <span class="font-black text-gray-900 text-lg">{{ $s[$r['n']] }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </section>

        <!-- ❓ Dynamic FAQ -->
        @elseif($section->type == 'faq')
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <h2 class="text-4xl font-black text-center text-gray-900 mb-8 md:mb-10">{{ $s['heading'] ?? 'Frequently Asked Questions' }}</h2>
                <div class="space-y-4">
                    @if(isset($s['faqs']) && is_array($s['faqs']))
                        @foreach($s['faqs'] as $faq)
                            <details class="group bg-white border border-gray-100 shadow-sm rounded-2xl [&::-webkit-details-marker]:hidden overflow-hidden">
                                <summary class="flex justify-between items-center font-black cursor-pointer list-none p-6 text-gray-800 text-lg hover:bg-gray-50 transition">
                                    <span>{{ $faq['q'] }}</span>
                                    <span class="transition-transform duration-300 group-open:rotate-180 text-gray-400 group-open:text-green-600 bg-gray-50 p-2 rounded-full">
                                        <svg fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                                    </span>
                                </summary>
                                <p class="text-gray-600 px-6 pb-6 leading-relaxed font-medium">{{ $faq['a'] }}</p>
                            </details>
                        @endforeach
                    @endif
                </div>
            </section>
        @elseif($section->type == 'featured_products')
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <h2 class="text-4xl font-black text-center text-gray-900 mb-8 md:mb-10">{{ $s['heading'] ?? 'Featured Products' }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @forelse($products->take($s['product_count'] ?? 4) as $product)
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
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400 font-bold">No products uploaded yet.</div>
                    @endforelse
                </div>
            </section>
        @elseif($section->type == 'video_banner')
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <div class="bg-gray-900 text-white rounded-[2rem] overflow-hidden shadow-xl grid grid-cols-1 md:grid-cols-2 gap-8 items-center p-8 md:p-12 border border-gray-800">
                    <div class="space-y-6 text-center md:text-left">
                        <h2 class="text-3xl md:text-5xl font-black leading-tight">{{ $s['heading'] ?? '' }}</h2>
                        <p class="text-lg text-gray-300">{{ $s['subheading'] ?? '' }}</p>
                        @if(!empty($s['btn_text']))
                            <a href="{{ $s['btn_link'] ?? '#products' }}" class="inline-block bg-white text-gray-900 font-black py-4 px-10 rounded-xl hover:bg-gray-100 transition shadow-lg hover:-translate-y-0.5">
                                {{ $s['btn_text'] }}
                            </a>
                        @endif
                    </div>
                    <div class="w-full aspect-video rounded-2xl overflow-hidden shadow-2xl border border-gray-800">
                        @if(!empty($s['video_url']))
                            @if(str_contains($s['video_url'], 'youtube.com') || str_contains($s['video_url'], 'youtu.be') || str_contains($s['video_url'], 'vimeo.com'))
                                <iframe class="w-full h-full" src="{{ $s['video_url'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                <video class="w-full h-full object-cover" controls src="{{ $s['video_url'] }}"></video>
                            @endif
                        @else
                            <div class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-400 font-bold">No Video Configured</div>
                        @endif
                    </div>
                </div>
            </section>
        @elseif($section->type == 'newsletter_form')
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 {{ $pt }} {{ $pb }}">
                <div class="bg-green-50 border border-green-100 rounded-[2rem] p-10 md:p-14 text-center space-y-6 shadow-sm">
                    <h2 class="text-3xl md:text-4xl font-black text-gray-900">{{ $s['heading'] ?? 'Subscribe' }}</h2>
                    <p class="text-gray-600 max-w-xl mx-auto text-base">{{ $s['subheading'] ?? '' }}</p>
                    <form class="newsletter-signup-form max-w-md mx-auto flex flex-col sm:flex-row gap-3">
                        <input type="email" placeholder="{{ $s['placeholder'] ?? 'Enter your email' }}" required class="flex-grow px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-600 font-semibold text-sm">
                        <button type="submit" class="btn-primary-custom font-bold py-3.5 px-8 rounded-xl transition text-sm shadow-md">
                            {{ $s['btn_text'] ?? 'Subscribe' }}
                        </button>
                    </form>
                </div>
            </section>
        @elseif($section->type == 'rich_text')
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center {{ $pt }} {{ $pb }}">
                <div class="space-y-6">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight">{{ $s['heading'] ?? '' }}</h2>
                    <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">{{ $s['text'] ?? '' }}</p>
                    @if(!empty($s['btn_text']))
                        <a href="{{ $s['btn_link'] ?? '#products' }}" class="btn-secondary-custom font-black py-4 px-10 rounded-xl shadow-lg hover:-translate-y-0.5 transition">
                            {{ $s['btn_text'] }}
                        </a>
                    @endif
                </div>
            </section>
        @endif
    @endforeach 

    <main id="products" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12 flex-grow border-t border-gray-100 mt-10">
        <h3 class="text-4xl font-black text-gray-900 mb-8 md:mb-10 text-center">Our Featured Products</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($products as $product)
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
            @empty
            <p class="col-span-full text-center text-gray-400 font-bold py-20 text-xl border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50">No products available yet. Start selling!</p>
            @endforelse
        </div>

        <!-- View All Products Button -->
        <div class="mt-16 text-center">
            <a href="/collection" class="btn-secondary-custom inline-flex items-center gap-3 font-black py-5 px-12 rounded-2xl transition-all shadow-xl hover:-translate-y-0.5 text-base">
                {{ $settings->enable_rtl ? 'ساری پروڈکٹس دیکھیں' : 'View All Products' }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
    </main>

    @if(isset($homepageReviews) && $homepageReviews->count() > 0)
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12 border-t border-gray-100 bg-gray-50/50 rounded-t-[3rem]">
        <h3 class="text-4xl font-black text-center text-gray-900 mb-4 leading-normal">
            {{ $settings->enable_rtl ? 'ہمارے مطمئن کسٹمرز کی رائے' : 'Customer Reviews' }}
        </h3>
        <p class="text-center text-gray-500 mb-8 md:mb-10 text-sm md:text-base font-bold">
            {{ $settings->enable_rtl ? 'یہ ریویوز ہمارے خریداروں نے براہِ راست پروڈکٹ پیج پر دیے ہیں' : 'These reviews are submitted directly by our buyers on the product page.' }}
        </p>
        
        <div class="swiper homepage-reviews-slider px-2 pb-14" dir="{{ $settings->enable_rtl ? 'rtl' : 'ltr' }}">
            <div class="swiper-wrapper">
                @foreach($homepageReviews as $rev)
                <div class="swiper-slide h-auto">
                    <div class="bg-gray-50/70 border border-gray-150 p-8 rounded-[2rem] shadow-sm relative pt-12 hover:shadow-xl transition-all duration-300 mx-2 mt-6 h-full flex flex-col justify-between hover:border-green-200">
                        <div class="absolute -top-4 -right-4 bg-[#16a34a] text-white w-10 h-10 flex items-center justify-center rounded-full text-2xl font-serif leading-none shadow-md z-10 select-none">”</div>
                        <div>
                            <div class="text-[#16a34a] text-center mb-6 text-xl tracking-widest flex justify-center">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $rev->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <p class="text-gray-700 text-base leading-relaxed mb-6 font-bold text-center">"{{ $rev->comment }}"</p>
                        </div>
                        <div>
                            <div class="border-t border-gray-200/60 my-4"></div>
                            <div class="flex flex-col items-center gap-2">
                                <span class="font-black text-gray-900 text-base text-center">{{ $rev->customer_name }}</span>
                                @if($rev->product)
                                    <a href="/product/{{ $rev->product->id }}" class="text-[10px] font-black text-[#16a34a] hover:underline uppercase tracking-wider text-center">
                                        {{ $settings->enable_rtl ? 'بابت: ' : 'Product: ' }}{{ $rev->product->name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination homepage-reviews-pagination mt-8"></div>
        </div>
    </section>
    @endif

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
        
        document.addEventListener("DOMContentLoaded", function() {
            if(document.querySelector('.hero-slider')) {
                new Swiper(".hero-slider", {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: false,
                    rewind: true,
                    autoplay: { delay: 5000, disableOnInteraction: false },
                    pagination: { el: ".swiper-pagination", clickable: true },
                    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" }
                });
            }
            if(document.querySelector('.testimonials-slider')) {
                new Swiper(".testimonials-slider", {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: false,
                    rewind: true,
                    autoplay: { delay: 4000, disableOnInteraction: false },
                    pagination: { el: ".swiper-pagination", clickable: true },
                    breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
                });
            }
            if(document.querySelector('.homepage-reviews-slider')) {
                new Swiper(".homepage-reviews-slider", {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: false,
                    rewind: true,
                    autoplay: { delay: 4000, disableOnInteraction: false },
                    pagination: { el: ".homepage-reviews-pagination", clickable: true },
                    breakpoints: { 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
                });
            }
        });
    </script>
    
    @if($settings->disable_inspect)
    <script>
        window.addEventListener("contextmenu", (e) => e.preventDefault());
        document.addEventListener("selectstart", (e) => e.preventDefault());
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img').forEach(img => { img.addEventListener('dragstart', (e) => e.preventDefault()); })
        });
    </script>
    @endif

    @if($settings->enable_sales_popup && is_array($settings->sales_popup_data))
    <div id="purelife-universal-pop" style="display: none; position: fixed; bottom: 30px; left: 15px; background: #ffffff; padding: 15px; border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); z-index: 10000; border: 1px solid #eee; width: 270px; direction: rtl; text-align: right; transition: opacity 0.5s ease-in-out; opacity: 0;">
      <div style="display: flex; align-items: flex-start;">
        <div style="background: #25d366; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 12px; color: white; flex-shrink: 0; margin-top: 5px;">✓</div>
        <div style="width: 100%;">
          <p class="pop-main-text" style="margin: 0; color: #111; font-size: 14px;"></p>
          <div id="pop-timer-eng" style="color: #25d366; font-size: 11px; font-weight: bold; font-family: sans-serif; margin-top: 4px; text-align: left;"></div>
        </div>
      </div>
    </div>
    <script>
      const ordersDatabase = {!! json_encode($settings->sales_popup_data) !!};
      let currentOrderCount = 0;
      const isRtl = {{ $settings->enable_rtl ? 'true' : 'false' }};
      function startPurelifePop() {
        if(ordersDatabase.length === 0) return;
        const o = ordersDatabase[currentOrderCount];
        if (isRtl) {
            document.querySelector('.pop-main-text').innerHTML = `<strong>${o.name}</strong> (${o.city}) نے <br><strong>${o.item}</strong> خریدا۔`;
        } else {
            document.querySelector('.pop-main-text').innerHTML = `<strong>${o.name}</strong> (${o.city}) purchased <br><strong>${o.item}</strong>.`;
        }
        document.getElementById('pop-timer-eng').innerText = o.time;
        
        const popDiv = document.getElementById('purelife-universal-pop');
        popDiv.style.display = 'block';
        setTimeout(() => { popDiv.style.opacity = '1'; }, 50);
        setTimeout(() => { 
          popDiv.style.opacity = '0';
          setTimeout(() => { popDiv.style.display = 'none'; }, 500);
          currentOrderCount = (currentOrderCount + 1) % ordersDatabase.length;
        }, 6500);
      }
      setInterval(startPurelifePop, 19000);
      setTimeout(startPurelifePop, 5000);
    </script>
    @endif
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

    @if($settings->custom_tracking_active && !empty($settings->custom_tracking_body))
    {!! $settings->custom_tracking_body !!}
    @endif
</body>
</html>