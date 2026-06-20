file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\collection.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\collection_buttons.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    with open(out_path, 'w', encoding='utf-8') as out:
        for idx, line in enumerate(lines):
            if '<button' in line or 'type="submit"' in line or 'reset' in line or 'filter' in line:
                if 'class="' in line:
                    out.write(f"{idx+1}: {line.strip()}\n")
                    
    print("Success writing collection page button lines.")
except Exception as e:
    print(f"Error: {e}")
