import re

file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\checkout.blade.php"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Inject Style block in head
    style_injection = """
    <style>
        /* Dynamic Stylesheet */
        .store-logo-img {
            height: {{ $settings->header_logo_height ?? 56 }}px !important;
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

    # 2. Update logo height
    old_logo_markup = '<img class="h-12 w-auto object-contain" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">'
    new_logo_markup = '<img class="w-auto object-contain store-logo-img" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">'
    content = content.replace(old_logo_markup, new_logo_markup)

    # 3. Update buttons
    # Place Order button
    content = content.replace("w-full bg-green-600 text-white font-black py-4.5 rounded-2xl text-lg hover:bg-green-700 transition shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2", "w-full btn-primary-custom text-white font-black py-4.5 rounded-2xl text-lg transition shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2")

    # Return to Shop link or other buttons if any
    # Let's check if there's any button like bg-green-50 or text-green-700
    content = content.replace("inline-block bg-green-50 text-green-700 border border-green-250 text-xs px-4 py-2 rounded-xl hover:bg-green-100 transition font-black font-sans flex items-center gap-1", "btn-secondary-custom text-xs px-4 py-2 rounded-xl transition font-black font-sans flex items-center gap-1")

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print("Successfully modified checkout.blade.php")

except Exception as e:
    print(f"Error modifying checkout: {e}")
