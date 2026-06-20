file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\mobile_menu_js.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    with open(out_path, 'w', encoding='utf-8') as out:
        for idx, line in enumerate(lines):
            if 'mobileMenu' in line or 'mobileMenuBtn' in line or 'toggle' in line:
                out.write(f"{idx+1}: {line.strip()}\n")
                
    print("Success writing mobile menu lines.")
except Exception as e:
    print(f"Error: {e}")
