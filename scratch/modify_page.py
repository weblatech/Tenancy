import re

file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\page.blade.php"

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

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print("Successfully modified page.blade.php")

except Exception as e:
    print(f"Error modifying page: {e}")
