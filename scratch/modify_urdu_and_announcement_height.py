import re

files_all = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\checkout.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\order-success.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\page.blade.php"
]

files_with_announcement = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\page.blade.php"
]

# 1. Update the RTL block in all 6 files
old_rtl_block = """    @if($settings->enable_rtl)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pakeezah-fonts/jameel-noori-nastaleeq@1.0.0/index.css">
    <style>
        body { direction: rtl; text-align: right; font-family: 'Jameel Noori Nastaleeq', sans-serif !important; }
    </style>
    @endif"""

new_rtl_block = """    @if($settings->enable_rtl)
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
    @endif"""

for file_path in files_all:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        content = content.replace(old_rtl_block, new_rtl_block)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Successfully updated RTL styling block in {file_path.split('\\')[-1]}")
    except Exception as e:
        print(f"Error modifying RTL in {file_path}: {e}")

# 2. Update dynamic marquee height in files containing announcement bar
for file_path in files_with_announcement:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Let's calculate $announcementHeight dynamically in PHP at the top of the file or just inside the style tag.
        # Let's insert the PHP calculation right before our dynamic stylesheet block.
        old_style_block_start = """    <style>
        /* Dynamic Stylesheet */"""
        
        new_style_block_start = """    @php
        $fontSizeNum = (int) filter_var($settings->announcement_font_size ?? '14px', FILTER_SANITIZE_NUMBER_INT);
        $announcementHeight = $fontSizeNum + ($settings->enable_rtl ? 24 : 16);
    @endphp
    <style>
        /* Dynamic Stylesheet */
        .marquee-container {
            height: {{ $announcementHeight }}px !important;
        }
        .marquee-text, .marquee-text-rtl {
            line-height: {{ $announcementHeight }}px !important;
        }"""

        content = content.replace(old_style_block_start, new_style_block_start)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Successfully updated marquee height calculation in {file_path.split('\\')[-1]}")
    except Exception as e:
        print(f"Error modifying marquee height in {file_path}: {e}")
