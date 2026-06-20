import re

files = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\page.blade.php"
]

for file_path in files:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # 1. Update dynamic height calculation formula
        old_height_calc = """    @php
        $fontSizeNum = (int) filter_var($settings->announcement_font_size ?? '14px', FILTER_SANITIZE_NUMBER_INT);
        $announcementHeight = $fontSizeNum + ($settings->enable_rtl ? 24 : 16);
    @endphp"""
        
        new_height_calc = """    @php
        $fontSizeNum = (int) filter_var($settings->announcement_font_size ?? '14px', FILTER_SANITIZE_NUMBER_INT);
        $announcementHeight = $fontSizeNum * 2 + ($settings->enable_rtl ? 20 : 12);
    @endphp"""
        content = content.replace(old_height_calc, new_height_calc)

        # 2. Add line-height 1.5 to static announcement bar
        old_static_css = """.marquee-text, .marquee-text-rtl, .static-announcement-bar {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
        }"""
        
        new_static_css = """.marquee-text, .marquee-text-rtl {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
        }
        .static-announcement-bar {
            font-size: {{ $settings->announcement_font_size ?? '14px' }} !important;
            line-height: 1.5 !important;
        }"""
        content = content.replace(old_static_css, new_static_css)

        # 3. Add mobile menu fallback
        old_mobile_menu = """<div id="mobileMenu" class="md:hidden hidden border-t p-4 space-y-2 shadow-lg store-header">
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
            @else
                <a href="/" class="header-menu-link block font-bold py-2 {{ request()->is('/') ? 'active' : '' }}">Home</a>
                <a href="/collection" class="header-menu-link block font-bold py-2 {{ request()->is('collection') ? 'active' : '' }}">Shop</a>
            @endif
        </div>"""
        content = content.replace(old_mobile_menu, new_mobile_menu)

        # 4. Bind mobile Menu Button and Cart Button text colors dynamically
        # Mobile hamburger btn
        content = content.replace(
            '<button id="mobileMenuBtn" class="md:hidden text-gray-800 focus:outline-none">',
            '<button id="mobileMenuBtn" class="md:hidden focus:outline-none" style="color: {{ $settings->header_menu_text ?? \'#1f2937\' }};">'
        )
        
        # Cart btn
        content = content.replace(
            '<button onclick="openCartDrawer()" class="text-gray-800 hover:text-green-600 transition flex items-center relative">',
            '<button onclick="openCartDrawer()" class="cart-icon-btn transition flex items-center relative" style="color: {{ $settings->header_menu_text ?? \'#1f2937\' }};">'
        )

        # Text logo backup span
        content = content.replace(
            '<span class="text-3xl font-black text-gray-900 tracking-tight">',
            '<span class="text-3xl font-black tracking-tight" style="color: {{ $settings->header_menu_text ?? \'#1f2937\' }};">'
        )
        content = content.replace(
            '<span class="text-2xl font-black text-gray-900 tracking-tight">',
            '<span class="text-2xl font-black tracking-tight" style="color: {{ $settings->header_menu_text ?? \'#1f2937\' }};">'
        )

        # Add hover styling for cart icon and hamburger icon in dynamic style tag
        dynamic_hover_css = """        .header-menu-link:hover {
            background-color: {{ $settings->header_menu_active_bg ?? '#f3f4f6' }} !important;
            color: {{ $settings->header_menu_active_text ?? '#16a34a' }} !important;
        }"""
        
        new_dynamic_hover_css = """        .header-menu-link:hover, .cart-icon-btn:hover, #mobileMenuBtn:hover {
            color: {{ $settings->header_menu_active_text ?? '#16a34a' }} !important;
        }"""
        content = content.replace(dynamic_hover_css, new_dynamic_hover_css)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Successfully modified all details in {file_path.split('\\')[-1]}")

    except Exception as e:
        print(f"Error modifying {file_path}: {e}")
