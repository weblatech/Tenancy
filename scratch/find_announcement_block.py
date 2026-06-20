file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\settings.blade.php"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    for idx, line in enumerate(lines):
        if "announcement_bg_color" in line:
            print(f"Match found on line {idx+1}: {line.strip()}")
            # Print 10 lines before and 25 lines after
            start = max(0, idx - 10)
            end = min(len(lines), idx + 25)
            print(f"--- Context (Lines {start+1} to {end}) ---")
            for i in range(start, end):
                print(f"{i+1}: {lines[i].rstrip()}")
            break
except Exception as e:
    print(f"Error: {e}")
