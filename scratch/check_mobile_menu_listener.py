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
        
        has_listener = "mobileMenuBtn" in content and "addEventListener" in content and "mobileMenu" in content
        print(f"File {file_path.split('\\')[-1]} has mobileMenuBtn listener: {has_listener}")
        
        # Let's search for how many script occurrences of mobileMenuBtn there are
        matches = [m.start() for m in re.finditer("mobileMenuBtn", content)]
        print(f"  Total occurrences of mobileMenuBtn: {len(matches)}")
    except Exception as e:
        print(f"Error: {e}")
