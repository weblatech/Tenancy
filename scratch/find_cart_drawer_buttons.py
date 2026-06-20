file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\partials\cart-drawer.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\cart_drawer_buttons.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    with open(out_path, 'w', encoding='utf-8') as out:
        for idx, line in enumerate(lines):
            if '<button' in line or 'onclick="' in line or 'href="' in line:
                if 'class="' in line:
                    out.write(f"{idx+1}: {line.strip()}\n")
                    
    print("Success writing cart drawer button lines.")
except Exception as e:
    print(f"Error: {e}")
