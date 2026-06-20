import re

file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Inject Style block in head
    style_injection = """
    <style>
        /* Dynamic Stylesheet */
        .marquee-text, .marquee-text-rtl, .static-announcement-bar {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
        }
        .store-logo-img {
            height: {{ $settings->header_logo_height ?? 56 }}px !important;
        }
        header.store-header {
            background-color: {{ $settings->header_menu_bg ?? '#ffffff' }} !important;
        }
        .header-menu-link {
            color: {{ $settings->header_menu_text ?? '#1f2937' }} !important;
            background-color: transparent;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .header-menu-link:hover {
            background-color: {{ $settings->header_menu_active_bg ?? '#f3f4f6' }} !important;
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
</head>"""
    
    content = content.replace("</head>", style_injection)

    # 2. Update announcement bar text
    content = content.replace('py-2 text-sm font-bold tracking-wide shadow-sm', 'py-2 text-sm font-bold tracking-wide shadow-sm static-announcement-bar')

    # 3. Update header, logo and menu links
    old_logo_markup = '<img class="h-14 w-auto object-contain" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">'
    new_logo_markup = '<img class="w-auto object-contain store-logo-img" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">'
    content = content.replace(old_logo_markup, new_logo_markup)

    old_header_markup = '<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">'
    new_header_markup = '<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100 store-header">'
    content = content.replace(old_header_markup, new_header_markup)

    # Replace desktop menu
    desktop_menu_pattern = r'<nav class="hidden md:flex space-x-10">.*?</nav>'
    new_desktop_menu = """<nav class="hidden md:flex space-x-4 items-center">
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
                        <a href="/" class="header-menu-link text-base font-bold {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        <a href="/collection" class="header-menu-link text-base font-bold {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
                    @endif
                </nav>"""
    
    content = re.sub(desktop_menu_pattern, new_desktop_menu, content, flags=re.DOTALL)

    # Replace mobile menu
    mobile_menu_pattern = r'<div id="mobileMenu" class="md:hidden hidden bg-white border-t p-4 space-y-2 shadow-lg">.*?(?=\n\s*</header>)'
    new_mobile_menu = """<div id="mobileMenu" class="md:hidden hidden border-t p-4 space-y-2 shadow-lg store-header">
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
            @endif
        </div>"""
    content = re.sub(mobile_menu_pattern, new_mobile_menu, content, flags=re.DOTALL)

    # 4. Bind dynamic colors for hero buttons and section buttons
    # Hero Button 1
    content = content.replace("bg-green-600 hover:bg-green-700 text-white font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base", "btn-primary-custom font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base")
    content = content.replace("bg-green-600 hover:bg-green-700 text-white font-black py-4 px-12 rounded-full shadow-xl transition transform hover:scale-105 inline-block text-base", "btn-primary-custom font-black py-4 px-12 rounded-full shadow-xl transition transform hover:scale-105 inline-block text-base")
    content = content.replace("font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base text-white bg-green-600 hover:bg-green-700", "btn-primary-custom font-black py-4 px-10 rounded-xl shadow-lg transition transform hover:scale-105 inline-block text-base")
    
    # Hero Button 2
    content = content.replace("bg-transparent hover:bg-white/10 text-white border-2 border-white/80 font-black py-4 px-10 rounded-xl transition inline-block text-base", "btn-secondary-custom font-black py-4 px-10 rounded-xl transition inline-block text-base border border-white")
    content = content.replace("bg-transparent hover:bg-white/10 text-white border-2 border-white/80 font-black py-4 px-12 rounded-full transition inline-block text-base", "btn-secondary-custom font-black py-4 px-12 rounded-full transition inline-block text-base border border-white")

    # Image with text button
    content = content.replace("inline-block bg-green-600 text-white font-black py-4 px-12 rounded-xl shadow-lg hover:bg-green-700 transition transform hover:-translate-y-1", "btn-primary-custom font-black py-4 px-12 rounded-xl shadow-lg transition transform hover:-translate-y-1")
    
    # Rich text button / Video banner button / Featured collection CTA
    content = content.replace("inline-block bg-gray-900 hover:bg-black text-white font-black py-4 px-10 rounded-xl shadow-lg hover:-translate-y-0.5 transition", "btn-secondary-custom font-black py-4 px-10 rounded-xl shadow-lg hover:-translate-y-0.5 transition")
    content = content.replace("inline-flex items-center gap-3 bg-gray-900 text-white font-black py-5 px-12 rounded-2xl hover:bg-black transition-all shadow-xl hover:shadow-gray-900/30 hover:-translate-y-0.5 text-base", "btn-secondary-custom inline-flex items-center gap-3 font-black py-5 px-12 rounded-2xl transition-all shadow-xl hover:-translate-y-0.5 text-base")

    # Newsletter submit button (if any)
    content = content.replace("bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-8 rounded-xl transition text-sm shadow-md shadow-green-950/10", "btn-primary-custom font-bold py-3.5 px-8 rounded-xl transition text-sm shadow-md")

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print("Successfully modified storefront.blade.php")

except Exception as e:
    print(f"Error modifying storefront: {e}")
