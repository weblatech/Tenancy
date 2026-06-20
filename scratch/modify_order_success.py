import re

file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\order-success.blade.php"

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
    # Continue Shopping button
    content = content.replace("bg-green-600 text-white font-black py-4 px-8 rounded-xl text-sm hover:bg-green-700 transition shadow-md", "btn-primary-custom text-white font-black py-4 px-8 rounded-xl text-sm transition shadow-md")
    
    # Back to Home button
    content = content.replace("border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold py-4 px-8 rounded-xl text-sm transition", "btn-secondary-custom font-bold py-4 px-8 rounded-xl text-sm transition")

    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
        
    print("Successfully modified order-success.blade.php")

except Exception as e:
    print(f"Error modifying order-success: {e}")
