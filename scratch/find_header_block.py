file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\settings.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\header_block_out.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    start_idx = -1
    for idx, line in enumerate(lines):
        if 'toggleAccordion(\'acc-header\')' in line:
            start_idx = idx
            break
            
    if start_idx != -1:
        with open(out_path, 'w', encoding='utf-8') as out:
            end = min(len(lines), start_idx + 150)
            for i in range(start_idx, end):
                out.write(f"{i+1}: {lines[i]}")
        print("Success writing header block context to file.")
    else:
        print("Could not find acc-header toggle accordion line.")
except Exception as e:
    print(f"Error: {e}")
