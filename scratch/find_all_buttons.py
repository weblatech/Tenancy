import re

files = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\checkout.blade.php"
]

for file_path in files:
    print(f"\n=== All Buttons in {file_path.split('\\')[-1]} ===")
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            for idx, line in enumerate(lines):
                if any(x in line for x in ['<button', 'type="submit"', 'role="button"']) or ('<a ' in line and 'bg-' in line):
                    print(f"Line {idx+1}: {line.strip()}")
    except Exception as e:
        print(f"Error: {e}")
