file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\settings.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\button_settings_block.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    start_idx = -1
    for idx, line in enumerate(lines):
        if "btn_add_to_cart_bg" in line:
            start_idx = idx
            break
            
    if start_idx != -1:
        with open(out_path, 'w', encoding='utf-8') as out:
            # Print 20 lines before and 60 lines after
            start = max(0, start_idx - 20)
            end = min(len(lines), start_idx + 60)
            for i in range(start, end):
                out.write(f"{i+1}: {lines[i]}")
        print("Success writing button settings block context to file.")
    else:
        print("Could not find btn_add_to_cart_bg line.")
except Exception as e:
    print(f"Error: {e}")
