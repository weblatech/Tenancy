files = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\page.blade.php"
]

target = '<div class="flex justify-between items-center h-20">'
replacement = '<div class="flex justify-between items-center py-4">'

for file_path in files:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        if target in content:
            content = content.replace(target, replacement)
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Successfully fixed header height in {file_path.split('\\')[-1]}")
        else:
            print(f"Target not found in {file_path.split('\\')[-1]}")
    except Exception as e:
        print(f"Error modifying {file_path}: {e}")
