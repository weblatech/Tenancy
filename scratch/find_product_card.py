file_path = r"d:\Projects\saas-ecommerce\resources\views\tenant\storefront.blade.php"
out_path = r"d:\Projects\saas-ecommerce\scratch\product_card_storefront.txt"

try:
    with open(file_path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        
    start_idx = -1
    for idx, line in enumerate(lines):
        if "product/{{ $product->id }}" in line:
            start_idx = idx
            break
            
    if start_idx != -1:
        with open(out_path, 'w', encoding='utf-8') as out:
            start = max(0, start_idx - 5)
            end = min(len(lines), start_idx + 40)
            for i in range(start, end):
                out.write(f"{i+1}: {lines[i]}")
        print("Success writing product card storefront context.")
    else:
        print("Could not find product details link in storefront.")
except Exception as e:
    print(f"Error: {e}")
