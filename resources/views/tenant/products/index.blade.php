<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Products Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
            background-size: cover;
            background-attachment: fixed;
        }
        .dotted-overlay {
            background-image: radial-gradient(#cbd5e1 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased bg-slate-50/50 pb-16 relative overflow-x-hidden">
    
    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>

    <!-- Top Premium Navigation Bar -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md relative z-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Products Manager</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ tenant_store_url() }}" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition">View Storefront ↗</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto mt-10 px-6">
        
        <!-- Banner Alert / Success Toast -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">✅</span>
                <span class="text-xs font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 space-y-6">
            <div class="absolute top-0 left-0 w-32 h-[4px] bg-blue-500"></div>
            
            <!-- Products Header Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Products Catalog 📦</h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Manage details, prices, images, and adjust stock counts dynamically.</p>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <input type="text" id="product-search-input" onkeyup="filterProducts()" placeholder="Search products (تلاش کریں)..." class="text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-indigo-500 transition duration-150 flex-1 sm:w-60">
                    <button onclick="openProductModal('add')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold py-3 px-5 rounded-xl text-xs shadow-md shadow-indigo-600/10 transition duration-150 flex items-center gap-1.5 shrink-0">
                        <span>+ Add Product</span>
                    </button>
                </div>
            </div>

            <!-- Products List Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-150 bg-white">
                <table class="min-w-full leading-normal text-left text-xs font-medium text-slate-600">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-150 text-slate-500 font-extrabold uppercase tracking-wider">
                            <th class="px-6 py-4">Image</th>
                            <th class="px-6 py-4">Product Details</th>
                            <th class="px-6 py-4">Price (قیمت)</th>
                            <th class="px-6 py-4">Compare Price</th>
                            <th class="px-6 py-4">Stock (اسٹاک)</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold" id="products-table-body">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/30 transition duration-150 product-row" data-name="{{ strtolower($product->name) }}">
                                <!-- Image -->
                                <td class="px-6 py-4">
                                    @if($product->image)
                                        <img src="{{ tenant_asset($product->image) }}" alt="Product" class="w-14 h-14 rounded-xl object-cover border border-slate-100 shadow-sm bg-slate-50">
                                    @else
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-[10px] font-bold">No Image</div>
                                    @endif
                                </td>
                                <!-- Details -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-slate-900">{{ $product->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium truncate max-w-xs mt-0.5">{{ $product->description }}</div>
                                </td>
                                <!-- Price -->
                                <td class="px-6 py-4 font-black text-emerald-600 text-sm">
                                    Rs. {{ number_format((float)$product->price) }}
                                </td>
                                <!-- Compare Price -->
                                <td class="px-6 py-4">
                                    @if($product->compare_price)
                                        <span class="text-slate-400 line-through">Rs. {{ number_format((float)$product->compare_price) }}</span>
                                    @else
                                        <span class="text-slate-300 italic text-[10px]">No discount</span>
                                    @endif
                                </td>
                                <!-- Stock -->
                                <td class="px-6 py-4">
                                    @if($product->stock <= 0)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-100 px-2.5 py-1 rounded-full text-[10px] font-black">Out of stock 🔴</span>
                                    @elseif($product->stock <= 5)
                                        <span class="bg-amber-50 text-amber-700 border border-amber-100 px-2.5 py-1 rounded-full text-[10px] font-black">{{ $product->stock }} Left ⚠️</span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full text-[10px] font-black">{{ $product->stock }} in stock ✓</span>
                                    @endif
                                </td>
                                <!-- Actions -->
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openProductModal('edit', {{ json_encode($product) }})" class="bg-slate-50 border border-slate-200 hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 text-slate-600 font-bold px-3 py-2 rounded-xl transition duration-150 flex items-center justify-center" title="Edit Product">
                                            ✏️ Edit
                                        </button>
                                        <form action="/shop/products/{{ $product->id }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? (کیا آپ واقعی یہ پروڈکٹ ڈیلیٹ کرنا چاہتے ہیں؟)');" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-rose-50 hover:bg-rose-100 border border-rose-100 text-rose-600 font-bold px-3 py-2 rounded-xl transition duration-150 flex items-center justify-center" title="Delete Product">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400 font-bold border-2 border-dashed border-slate-150 bg-slate-50/50 rounded-2xl">
                                    <span class="text-4xl block mb-3">📭</span>
                                    No products have been added yet. Click "+ Add Product" to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- ========================================================= -->
    <!-- ================= MODALS & OVERLAYS ===================== -->
    <!-- ========================================================= -->

    <!-- Add/Edit Product Modal Overlay -->
    <div id="product-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" onclick="closeProductModal()"></div>
        
        <!-- Modal Wrapper -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100 transform transition-all p-6 md:p-8 space-y-6">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2" id="product-modal-title">
                            📦 Create New Product
                        </h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Populate item card specifications in your storefront catalogue.</p>
                    </div>
                    <button onclick="closeProductModal()" class="text-slate-400 hover:text-rose-500 bg-slate-50 hover:bg-rose-50 border border-slate-200/60 shadow-sm p-2 rounded-xl transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="product-form" action="/shop/add-product" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    
                    <!-- Name / Title -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Product Title / Name (پروڈکٹ کا نام) <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="product-name" required placeholder="e.g. Pure Honey Jam" class="w-full text-xs font-bold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-indigo-500 transition duration-150">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Product Description (تفصیل)</label>
                        <textarea name="description" id="product-description" class="hidden"></textarea>
                        <div id="product-description-editor" class="bg-white border border-slate-200 rounded-xl text-xs leading-relaxed" style="height: 200px;"></div>
                    </div>

                    <!-- Variants Section -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Product Variants (پروڈکٹ کے ویرینٹ)</label>
                        <textarea name="variants_text" id="product-variants-text" rows="2" placeholder="e.g. Size: S, M, L&#10;Color: Red, Blue, Green (One variant option per line)" class="w-full text-xs font-semibold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-indigo-500 transition duration-150 leading-relaxed"></textarea>
                        <p class="text-[9px] text-slate-400 mt-1">Type option name and comma separated values, one option per line.</p>
                        
                        <!-- Hidden input to store JSON serialization -->
                        <input type="hidden" name="variant_combinations_json" id="product-variant-combinations-json" value="">

                        <!-- Container for dynamic table -->
                        <div id="product-variant-combinations-container" class="hidden mt-4 pt-4 border-t border-slate-100 space-y-4">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Variant Price & Stock Configuration</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-[11px] font-semibold border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-150 text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-2.5">Variant Combination</th>
                                            <th class="py-2.5 w-28">Price (Rs.)</th>
                                            <th class="py-2.5 w-28">Compare At (Rs.)</th>
                                            <th class="py-2.5 w-20">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-variant-combinations-tbody" class="divide-y divide-slate-100 text-slate-700">
                                        <!-- Rows added by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bundle Deal Section -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_bundle" id="product-is-bundle" value="1" onchange="toggleBundleFields()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Enable Bundle Deal (بنڈل ڈیل)</span>
                        </label>
                        <div id="bundle-fields-container" class="hidden space-y-3 pt-2 border-t border-slate-200/50">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bundle Header Title (ڈیل ہیڈر ٹائٹل)</label>
                                    <input type="text" name="bundle_header_title" id="product-bundle-header-title" placeholder="e.g. بڑی عید کی بڑی آفر" value="بڑی عید کی بڑی آفر" class="w-full text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bundle Header Badge (بیج)</label>
                                    <input type="text" name="bundle_header_badge" id="product-bundle-header-badge" placeholder="e.g. LIMITED STOCK" value="LIMITED STOCK" class="w-full text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bundle Block Color (بنڈل بلاک کلر)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="product-bundle-color-primary-picker" value="#16a34a" oninput="document.getElementById('product-bundle-color-primary').value = this.value" class="w-9 h-9 border border-slate-200 rounded-lg cursor-pointer bg-transparent p-0">
                                        <input type="text" name="bundle_color_primary" id="product-bundle-color-primary" placeholder="#16a34a" value="#16a34a" oninput="document.getElementById('product-bundle-color-primary-picker').value = this.value" class="flex-1 text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Banner Text Color (ٹیکسٹ کلر)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="product-bundle-color-text-picker" value="#ffffff" oninput="document.getElementById('product-bundle-color-text').value = this.value" class="w-9 h-9 border border-slate-200 rounded-lg cursor-pointer bg-transparent p-0">
                                        <input type="text" name="bundle_color_text" id="product-bundle-color-text" placeholder="#ffffff" value="#ffffff" oninput="document.getElementById('product-bundle-color-text-picker').value = this.value" class="flex-1 text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-3 pt-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bundle Options / Packages (بنڈل پیکجز)</label>
                                <div id="bundle-options-list" class="space-y-3">
                                    <!-- Dynamic rows added here -->
                                </div>
                                <button type="button" onclick="addBundleOptionRow()" class="mt-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold px-3.5 py-2 rounded-lg text-[10px] transition duration-150 flex items-center gap-1">
                                    <span>+ Add Option Package</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Special Discount Section -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_discount" id="product-is-discount" value="1" onchange="toggleDiscountFields()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Enable Special Discount Deal (ڈسکاؤنٹ ڈیل)</span>
                        </label>
                        <div id="discount-fields-container" class="hidden space-y-3 pt-2 border-t border-slate-200/50">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Discount Badge (e.g. 50% OFF)</label>
                                    <input type="text" name="discount_badge" id="product-discount-badge" placeholder="e.g. Special Deal" class="w-full text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Discount Terms</label>
                                    <input type="text" name="discount_terms" id="product-discount-terms" placeholder="e.g. Free shipping on this special deal" class="w-full text-xs font-bold px-3 py-2 border border-slate-200 rounded-lg outline-none focus:bg-white focus:border-indigo-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Compare Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sale Price (قیمت) (Rs.) <span class="text-rose-500">*</span></label>
                            <input type="number" name="price" id="product-price" required min="0" placeholder="e.g. 1500" class="w-full text-xs font-extrabold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-indigo-500 transition duration-150">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Compare-at Price (Rs.)</label>
                            <input type="number" name="compare_price" id="product-compare-price" min="0" placeholder="e.g. 2000" class="w-full text-xs font-extrabold px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-indigo-500 transition duration-150">
                        </div>
                    </div>

                    <!-- Dynamic Stock Selector & Image Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Premium Stock spinner -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Available Stock Count (اسٹاک)</label>
                            <div class="flex items-center w-full bg-slate-50 border border-slate-200 rounded-xl overflow-hidden select-none">
                                <button type="button" onclick="adjustStockValue(-1)" class="w-12 h-11 flex items-center justify-center font-extrabold text-slate-600 hover:bg-slate-200/50 hover:text-slate-900 border-r border-slate-200 text-lg transition duration-150">-</button>
                                <input type="number" name="stock" id="product-stock" value="10" min="0" required class="flex-1 text-center bg-transparent border-none outline-none font-black text-sm text-slate-800" style="-moz-appearance: textfield;">
                                <button type="button" onclick="adjustStockValue(1)" class="w-12 h-11 flex items-center justify-center font-extrabold text-slate-600 hover:bg-slate-200/50 hover:text-slate-900 border-l border-slate-200 text-lg transition duration-150">+</button>
                            </div>
                        </div>

                        <!-- Image File Uploader -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Images (تصاویر)</label>
                            <div class="relative border border-slate-200 border-dashed bg-slate-50 hover:bg-indigo-50/20 hover:border-indigo-400 rounded-xl p-3.5 text-center cursor-pointer transition duration-150">
                                <input type="file" name="images[]" id="images-uploader" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <span class="text-xs font-black text-indigo-600 flex items-center justify-center gap-1">📸 Upload Images</span>
                            </div>
                        </div>

                    </div>

                    <!-- Uploader Preview Grid -->
                    <div id="uploader-preview-container" class="hidden space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Images Queue Preview</label>
                        <div id="uploader-preview-grid" class="grid grid-cols-4 gap-2"></div>
                    </div>

                    <!-- Action Button Submit -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" onclick="closeProductModal()" class="text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-100 px-5 py-3 rounded-xl transition duration-150">
                            Cancel
                        </button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-3 rounded-xl shadow-md shadow-indigo-600/10 text-xs transition duration-150" id="product-modal-submit-btn">
                            Save Product
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- JavaScript Controllers -->
    <script>
        let bundleOptionIndex = 0;
        var productQuill;

        function addBundleOptionRow(data = null) {
            const list = document.getElementById('bundle-options-list');
            if (!list) return;
            const row = document.createElement('div');
            row.className = 'p-3 border border-slate-200 bg-slate-50/30 rounded-xl space-y-3 relative bundle-option-row';
            row.setAttribute('data-index', bundleOptionIndex);

            const title = data ? data.title : '';
            const price = data ? data.price : '';
            const compare_price = data ? (data.compare_price || '') : '';
            const badge = data ? data.badge : '';
            const label = data ? data.label : '';
            const image = data ? data.image : '';

            row.innerHTML = `
                <button type="button" onclick="this.closest('.bundle-option-row').remove()" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 font-bold text-base transition" title="Remove">&times;</button>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 mb-0.5">Option Label / Title *</label>
                        <input type="text" name="bundle_options[${bundleOptionIndex}][title]" value="${title}" placeholder="e.g. Buy 20 Capsule + 2 FREE" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 mb-0.5">Savings Badge</label>
                        <input type="text" name="bundle_options[${bundleOptionIndex}][badge]" value="${badge}" placeholder="e.g. BACHAT RS 500" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 mb-0.5">Price (Rs.) *</label>
                        <input type="number" name="bundle_options[${bundleOptionIndex}][price]" value="${price}" placeholder="e.g. 3500" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 mb-0.5">Compare Price (Rs.)</label>
                        <input type="number" name="bundle_options[${bundleOptionIndex}][compare_price]" value="${compare_price}" placeholder="e.g. 4000" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 mb-0.5">Ribbon Label</label>
                        <input type="text" name="bundle_options[${bundleOptionIndex}][label]" value="${label}" placeholder="e.g. SUPER SAVER" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
<label class="block text-[9px] font-bold text-slate-400 mb-0.5">Bundle Image</label>
                    <input type="file" name="bundle_options[${bundleOptionIndex}][image]" accept="image/*" class="w-full text-[10px] text-slate-500 file:mr-3 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[10px] file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    ${image ? `<input type="hidden" name="bundle_options[${bundleOptionIndex}][existing_image]" value="${image}"><p class="text-[9px] text-emerald-600 mt-0.5">✓ Image: ${image.split('/').pop()}</p>` : ''}
                </div>
            `;

            list.appendChild(row);
            bundleOptionIndex++;
        }

        // Product Search Filter logic
        function filterProducts() {
            const query = document.getElementById('product-search-input').value.toLowerCase();
            document.querySelectorAll('.product-row').forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(query)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        }

        // --- VARIANT COMBINATIONS MATRIX GENERATOR ---
        let activeVariantCombinations = [];

        function getCartesianProduct(arrays) {
            return arrays.reduce((acc, curr) => {
                const res = [];
                acc.forEach(a => {
                    curr.forEach(b => {
                        res.push(a.concat([b]));
                    });
                });
                return res;
            }, [[]]);
        }

        function parseVariantsText(text) {
            const parsed = {};
            const lines = text.split('\n').map(l => l.trim()).filter(l => l !== '');
            if (lines.length === 0) return parsed;

            // Check if there is at least one line with a colon
            const hasColon = lines.some(line => line.includes(':'));

            if (hasColon) {
                lines.forEach(line => {
                    const parts = line.split(':');
                    if (parts.length >= 2) {
                        const name = parts[0].trim();
                        const vals = parts.slice(1).join(':').split(',').map(v => v.trim()).filter(v => v !== '');
                        if (name && vals.length > 0) {
                            parsed[name] = vals;
                        }
                    }
                });
            } else {
                parsed['Option'] = lines;
            }
            return parsed;
        }

        function rebuildVariantCombinationsTable() {
            const textarea = document.getElementById('product-variants-text');
            const container = document.getElementById('product-variant-combinations-container');
            const tbody = document.getElementById('product-variant-combinations-tbody');
            if (!textarea || !container || !tbody) return;

            const parsed = parseVariantsText(textarea.value);
            const keys = Object.keys(parsed);
            
            if (keys.length === 0) {
                container.classList.add('hidden');
                document.getElementById('product-variant-combinations-json').value = '';
                activeVariantCombinations = [];
                tbody.innerHTML = '';
                return;
            }

            container.classList.remove('hidden');

            const optionValues = keys.map(key => parsed[key]);
            const combinations = getCartesianProduct(optionValues);

            // Fetch default price and stock values to populate as defaults
            const defaultPrice = document.getElementById('product-price')?.value || '';
            const defaultComparePrice = document.getElementById('product-compare-price')?.value || '';
            const defaultStock = document.getElementById('product-stock')?.value || '10';

            // Preserve old input values if the combination is still valid
            const oldCombinationsMap = {};
            activeVariantCombinations.forEach(c => {
                oldCombinationsMap[c.name] = c;
            });

            const newCombinations = [];
            tbody.innerHTML = '';

            combinations.forEach((combo, index) => {
                const comboName = combo.join(' / ');
                const comboObj = {};
                keys.forEach((key, kIdx) => {
                    comboObj[key] = combo[kIdx];
                });

                // Check if we already have input values for this combination name
                const old = oldCombinationsMap[comboName];
                const price = old ? old.price : defaultPrice;
                const comparePrice = old ? old.compare_price : defaultComparePrice;
                const stock = old ? old.stock : defaultStock;

                newCombinations.push({
                    name: comboName,
                    combination: comboObj,
                    price: price !== '' ? parseFloat(price) : null,
                    compare_price: comparePrice !== '' ? parseFloat(comparePrice) : null,
                    stock: parseInt(stock) || 0
                });

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/50 transition';
                tr.innerHTML = `
                    <td class="py-3 font-bold text-slate-800">${comboName}</td>
                    <td class="py-3 pr-2">
                        <input type="number" data-index="${index}" data-field="price" value="${price}" required
                            class="variant-input w-full px-2.5 py-1.5 border border-slate-200 rounded-lg bg-white outline-none focus:border-indigo-500 text-xs font-semibold">
                    </td>
                    <td class="py-3 pr-2">
                        <input type="number" data-index="${index}" data-field="compare_price" value="${comparePrice}"
                            class="variant-input w-full px-2.5 py-1.5 border border-slate-200 rounded-lg bg-white outline-none focus:border-indigo-500 text-xs font-semibold">
                    </td>
                    <td class="py-3">
                        <input type="number" data-index="${index}" data-field="stock" value="${stock}" required
                            class="variant-input w-full px-2.5 py-1.5 border border-slate-200 rounded-lg bg-white outline-none focus:border-indigo-500 text-xs font-semibold">
                    </td>
                `;
                tbody.appendChild(tr);
            });

            activeVariantCombinations = newCombinations;
            syncVariantCombinationsJson();

            // Setup input change event listeners
            tbody.querySelectorAll('.variant-input').forEach(input => {
                input.addEventListener('input', (e) => {
                    const idx = parseInt(e.target.getAttribute('data-index'));
                    const field = e.target.getAttribute('data-field');
                    let val = e.target.value;
                    if (field === 'price' || field === 'compare_price' || field === 'stock') {
                        val = val !== '' ? parseFloat(val) : null;
                    }
                    activeVariantCombinations[idx][field] = val;
                    syncVariantCombinationsJson();
                });
            });
        }

        function syncVariantCombinationsJson() {
            document.getElementById('product-variant-combinations-json').value = JSON.stringify(activeVariantCombinations);
        }

        // Add / Edit Product Modal Controllers
        function openProductModal(mode, productData = null) {
            const modal = document.getElementById('product-modal');
            const titleEl = document.getElementById('product-modal-title');
            const formEl = document.getElementById('product-form');
            const submitBtn = document.getElementById('product-modal-submit-btn');
            
            if (mode === 'add') {
                titleEl.innerHTML = '📦 Create New Product';
                formEl.action = '/shop/add-product';
                submitBtn.innerHTML = 'Save Product';
                
                // Clear fields
                document.getElementById('product-name').value = '';
                document.getElementById('product-description').value = '';
                if (productQuill) productQuill.root.innerHTML = '';
                document.getElementById('product-price').value = '';
                document.getElementById('product-compare-price').value = '';
                document.getElementById('product-stock').value = '10';
                document.getElementById('product-variants-text').value = '';
                
                activeVariantCombinations = [];
                var variantTbody = document.getElementById('product-variant-combinations-tbody');
                if (variantTbody) variantTbody.innerHTML = '';
                rebuildVariantCombinationsTable();

                document.getElementById('product-is-bundle').checked = false;
                document.getElementById('product-bundle-header-title').value = 'بڑی عید کی بڑی آفر';
                document.getElementById('product-bundle-header-badge').value = 'LIMITED STOCK';
                document.getElementById('product-bundle-color-primary').value = '#16a34a';
                document.getElementById('product-bundle-color-primary-picker').value = '#16a34a';
                document.getElementById('product-bundle-color-text').value = '#ffffff';
                document.getElementById('product-bundle-color-text-picker').value = '#ffffff';
                document.getElementById('bundle-options-list').innerHTML = '';
                addBundleOptionRow();
                document.getElementById('product-is-discount').checked = false;
                document.getElementById('product-discount-badge').value = '';
                document.getElementById('product-discount-terms').value = '';
                toggleBundleFields();
                toggleDiscountFields();
                
                // Make image required on add
                document.getElementById('images-uploader').required = true;
            } 
            else if (mode === 'edit' && productData) {
                titleEl.innerHTML = `✏️ Edit Product: ${productData.name}`;
                formEl.action = `/shop/products/${productData.id}/edit`;
                submitBtn.innerHTML = 'Update Details';
                
                // Populate fields
                document.getElementById('product-name').value = productData.name;
                const desc = productData.description || '';
                document.getElementById('product-description').value = desc;
                if (productQuill) productQuill.root.innerHTML = desc;
                document.getElementById('product-price').value = Math.round(productData.price);
                document.getElementById('product-compare-price').value = productData.compare_price ? Math.round(productData.compare_price) : '';
                document.getElementById('product-stock').value = productData.stock || '0';
                
                // Parse and set variants
                let variantsText = '';
                if (productData.variants) {
                    for (let [key, val] of Object.entries(productData.variants)) {
                        variantsText += `${key}: ${val.join(', ')}\n`;
                    }
                }
                document.getElementById('product-variants-text').value = variantsText;

                // Load saved combinations
                activeVariantCombinations = productData.variant_combinations || [];
                rebuildVariantCombinationsTable();

                // Set Bundle Deal
                document.getElementById('product-is-bundle').checked = !!productData.is_bundle;
                document.getElementById('product-bundle-header-title').value = productData.bundle_header_title || 'بڑی عید کی بڑی آفر';
                document.getElementById('product-bundle-header-badge').value = productData.bundle_header_badge || 'LIMITED STOCK';
                
                const colorPrimary = productData.bundle_color_primary || '#16a34a';
                const colorText = productData.bundle_color_text || '#ffffff';
                document.getElementById('product-bundle-color-primary').value = colorPrimary;
                document.getElementById('product-bundle-color-primary-picker').value = colorPrimary;
                document.getElementById('product-bundle-color-text').value = colorText;
                document.getElementById('product-bundle-color-text-picker').value = colorText;

                const list = document.getElementById('bundle-options-list');
                list.innerHTML = '';
                if (productData.bundle_options && Array.isArray(productData.bundle_options) && productData.bundle_options.length > 0) {
                    productData.bundle_options.forEach(opt => addBundleOptionRow(opt));
                } else {
                    addBundleOptionRow();
                }
                toggleBundleFields();
 
                // Set Discount Deal
                document.getElementById('product-is-discount').checked = !!productData.is_discount;
                document.getElementById('product-discount-badge').value = productData.discount_badge || '';
                document.getElementById('product-discount-terms').value = productData.discount_terms || '';
                toggleDiscountFields();
                
                // Make image optional on edit
                document.getElementById('images-uploader').required = false;
            }
            
            modal.classList.remove('hidden');
        }

        function closeProductModal() {
            document.getElementById('product-modal').classList.add('hidden');
        }

        function toggleBundleFields() {
            const checked = document.getElementById('product-is-bundle').checked;
            const container = document.getElementById('bundle-fields-container');
            if (checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function toggleDiscountFields() {
            const checked = document.getElementById('product-is-discount').checked;
            const container = document.getElementById('discount-fields-container');
            if (checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        // Plus/Minus Stock Spinner Incrementor
        function adjustStockValue(amount) {
            const stockInput = document.getElementById('product-stock');
            if (!stockInput) return;
            
            let val = parseInt(stockInput.value, 10);
            if (isNaN(val)) val = 0;
            
            val += amount;
            if (val < 0) val = 0;
            
            stockInput.value = val;
        }

        // Handle Image Previews on product add/edit modals
        document.getElementById('images-uploader').addEventListener('change', function(e) {
            const grid = document.getElementById('uploader-preview-grid');
            const container = document.getElementById('uploader-preview-container');
            grid.innerHTML = '';
            
            const files = e.target.files;
            if (files.length > 0) {
                container.classList.remove('hidden');
                
                Array.from(files).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const imgWrapper = document.createElement('div');
                        imgWrapper.className = 'aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50 relative';
                        imgWrapper.innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
                        grid.appendChild(imgWrapper);
                    }
                    reader.readAsDataURL(file);
                });
            } else {
                container.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            productQuill = new Quill('#product-description-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });

            // Form submit event to copy Quill content back to hidden textarea
            const form = document.getElementById('product-form');
            if (form) {
                form.addEventListener('submit', () => {
                    if (productQuill) {
                        document.getElementById('product-description').value = productQuill.root.innerHTML;
                    }
                });
            }

            // Set up variants matrix listeners
            const variantsTextarea = document.getElementById('product-variants-text');
            if (variantsTextarea) {
                variantsTextarea.addEventListener('input', rebuildVariantCombinationsTable);
                document.getElementById('product-price')?.addEventListener('input', rebuildVariantCombinationsTable);
                document.getElementById('product-compare-price')?.addEventListener('input', rebuildVariantCombinationsTable);
                document.getElementById('product-stock')?.addEventListener('input', rebuildVariantCombinationsTable);
            }
        });
    </script>
</body>
</html>
