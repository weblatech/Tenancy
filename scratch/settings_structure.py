import re

file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\settings.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\settings_structure_out.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
        
    with open(out_path, 'w', encoding='utf-8') as out:
        out.write("=== Tabs or sections in settings.blade.php ===\n")
        # Let's search for buttons that toggle sections, or section containers
        # Look for things like onclick="showTab( or showPanel(
        tab_matches = re.findall(r'(<button[^>]*class="[^"]*tab[^"]*"[^>]*>.*?<\/button>)', content, re.DOTALL | re.IGNORECASE)
        for t in tab_matches:
            out.write(f"Tab button: {t.strip()}\n")
            
        out.write("\n=== Headings ===\n")
        headings = re.findall(r'(<h[2-4][^>]*>.*?<\/h[2-4]>)', content, re.DOTALL | re.IGNORECASE)
        for h in headings:
            out.write(f"Heading: {h.strip()}\n")
            
        out.write("\n=== Form fields / inputs matching theme, button, hero, header ===\n")
        inputs = re.findall(r'(<input[^>]*name="[^"]*(?:color|bg|logo|announcement|btn|hero)[^"]*"[^>]*>)', content, re.IGNORECASE)
        for inp in inputs:
            out.write(f"Input: {inp.strip()}\n")

    print("Done writing to scratch/settings_structure_out.txt")
except Exception as e:
    print(f"Error: {e}")
