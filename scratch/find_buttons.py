import re

files = [
    r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\product-detail.blade.php",
    r"d:\Projects\saas-ecommerce\resources\views\tenant\checkout.blade.php"
]

for file_path in files:
    print(f"\n=== Buttons in {file_path.split('\\')[-1]} ===")
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # Find all <button> tags and <a> tags with class containing btn/bg-
            matches = re.findall(r'(<(?:button|a)[^>]*class="[^"]*(?:btn|bg-|text-white)[^"]*"[^>]*>.*?<\/(?:button|a)>)', content, re.DOTALL | re.IGNORECASE)
            for m in matches[:15]:  # print first 15 matches
                # print first line of each match
                first_line = m.split('\n')[0].strip()
                print(f"  {first_line[:120]}")
    except Exception as e:
        print(f"Error reading {file_path}: {e}")
