<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Shopify Style</title>
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
                    <a href="/shop/products" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Back to Products</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="http://{{ $tenantId }}.localhost:8000" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition">View Storefront</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="relative z-10 max-w-5xl mx-auto mt-10 px-6 pb-20">
        
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Add Product </h1>
                <p class="text-slate-500 mt-1 font-medium">Create a new item in your storefront catalogue.</p>
            </div>
            <a href="/shop/products" class="text-slate-400 hover:text-slate-700 font-bold text-xs transition">&larr; Cancel</a>
        </div>

            <!-- Form -->
            <form id="add-product-form" action="/shop/add-product" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8" novalidate>
                @csrf
                
                <!-- Left Column (Primary settings - 2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Title & Description Card -->
                    <div class="card-premium relative overflow-hidden p-6 md:p-8 rounded-3xl space-y-5">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Product Details</h3>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Product Title / Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="product-name-input"
                                placeholder="e.g. Pure Honey Jam"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="product_description" class="hidden"></textarea>
                            <div id="product-description-editor" class="bg-white border border-gray-200 rounded-xl text-sm leading-relaxed" style="height: 200px;"></div>
                        </div>
                    </div>

                    <!-- Variants Section -->
                    <div class="card-premium relative overflow-hidden p-6 md:p-8 rounded-3xl space-y-4">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Product Variants</h3>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Variants (One per line)</label>
                            <textarea name="variants_text" id="product-variants-text" rows="3" 
                                placeholder="e.g. Size: S, M, L&#10;Color: Red, Blue, Green"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-medium outline-none focus:border-blue-500 transition leading-relaxed"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Type option name and comma separated values, one option per line.</p>
                        </div>

                        <!-- Hidden input to store JSON serialization -->
                        <input type="hidden" name="variant_combinations_json" id="variant-combinations-json" value="">

                        <!-- Container for dynamic table -->
                        <div id="variant-combinations-container" class="hidden mt-4 pt-4 border-t border-gray-100 space-y-4">
                            <h4 class="text-sm font-bold text-gray-800">Variant Price & Stock Configuration</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs font-semibold border-collapse">
                                    <thead>
                                        <tr class="border-b border-gray-150 text-gray-400 font-bold uppercase tracking-wider">
                                            <th class="py-2.5">Variant Combination</th>
                                            <th class="py-2.5 w-32">Price (Rs.)</th>
                                            <th class="py-2.5 w-32">Compare At (Rs.)</th>
                                            <th class="py-2.5 w-24">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variant-combinations-tbody" class="divide-y divide-gray-100 text-gray-700">
                                        <!-- Rows added by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Bundle Deal Section -->
                    <div class="card-premium relative overflow-hidden p-6 md:p-8 rounded-3xl space-y-4">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Bundle Deal Settings</h3>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_bundle" id="product-is-bundle" value="1" onchange="toggleBundleFields()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-gray-700">Enable Bundle Deal</span>
                        </label>
                        <div id="bundle-fields-container" class="hidden space-y-4 pt-3 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Bundle Header Title</label>
                                    <input type="text" name="bundle_header_title" id="product-bundle-header-title" placeholder="e.g. Buy Big Eid Big Offer" value="Buy Big Eid Big Offer" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-medium outline-none focus:border-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Bundle Header Badge</label>
                                    <input type="text" name="bundle_header_badge" id="product-bundle-header-badge" placeholder="e.g. LIMITED STOCK" value="LIMITED STOCK" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-medium outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Bundle Block Primary Color</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="product-bundle-color-primary-picker" value="#16a34a" oninput="document.getElementById('product-bundle-color-primary').value = this.value" class="w-12 h-12 border border-gray-200 rounded-xl cursor-pointer bg-transparent p-0">
                                        <input type="text" name="bundle_color_primary" id="product-bundle-color-primary" placeholder="#16a34a" value="#16a34a" oninput="document.getElementById('product-bundle-color-primary-picker').value = this.value" class="flex-1 px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-blue-500 transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Banner Title Text Color</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="product-bundle-color-text-picker" value="#ffffff" oninput="document.getElementById('product-bundle-color-text').value = this.value" class="w-12 h-12 border border-gray-200 rounded-xl cursor-pointer bg-transparent p-0">
                                        <input type="text" name="bundle_color_text" id="product-bundle-color-text" placeholder="#ffffff" value="#ffffff" oninput="document.getElementById('product-bundle-color-text-picker').value = this.value" class="flex-1 px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-blue-500 transition">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-3 pt-3">
                                <label class="block text-sm font-bold text-gray-700">Bundle Options / Packages</label>
                                <div id="bundle-options-list" class="space-y-4">
                                    <!-- Dynamic rows added here -->
                                </div>
                                <button type="button" onclick="addBundleOptionRow()" class="mt-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-bold px-4 py-2 rounded-xl text-xs transition duration-150 flex items-center gap-1">
                                    <span>+ Add Option Package</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Special Discount Section -->
                    <div class="card-premium relative overflow-hidden p-6 md:p-8 rounded-3xl space-y-4">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Special Discount Settings</h3>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_discount" id="product-is-discount" value="1" onchange="toggleDiscountFields()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-bold text-gray-700">Enable Special Discount Deal</span>
                        </label>
                        <div id="discount-fields-container" class="hidden space-y-4 pt-3 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Discount Badge (e.g. 50% OFF)</label>
                                    <input type="text" name="discount_badge" id="product-discount-badge" placeholder="e.g. Special Deal" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-medium outline-none focus:border-blue-500 transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Discount Terms</label>
                                    <input type="text" name="discount_terms" id="product-discount-terms" placeholder="e.g. Free shipping on this special deal" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-medium outline-none focus:border-blue-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div class="card-premium relative overflow-hidden p-6 md:p-8 rounded-3xl space-y-5">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Pricing</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Sale Price (Rs.) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" id="product-price" required min="0" placeholder="e.g. 1500"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-extrabold outline-none focus:border-blue-500 transition">
                                <span class="text-[10px] text-gray-400 mt-1 block">Active storefront discount price.</span>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Compare-at Price (Rs.)</label>
                                <input type="number" name="compare_price" id="product-compare-price" min="0" placeholder="e.g. 2000"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-extrabold outline-none focus:border-blue-500 transition">
                                <span class="text-[10px] text-gray-400 mt-1 block">Original price for strikethrough styling.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Media uploads & saves - 1 col) -->
                <div class="space-y-6">
                    
                    <!-- Media Card -->
                    <div class="card-premium relative overflow-hidden p-6 rounded-3xl space-y-5">
                        <div class="absolute top-0 left-0 w-16 h-[3px] bg-blue-500"></div>
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Product Images</h3>
                        
                        <!-- File Upload input -->
                        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center cursor-pointer hover:border-blue-500 transition relative bg-gray-50 hover:bg-blue-50/20">
                            <input type="file" name="images[]" id="images-uploader" accept="image/*" multiple
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <span class="text-3xl block mb-2"></span>
                            <span class="text-xs font-black text-blue-600 block">Click to Upload Images</span>
                            <span class="text-[9px] text-gray-400 mt-1 block">Select multiple files (No Limit)</span>
                        </div>

                        <!-- Live preview container -->
                        <div id="preview-container" class="space-y-3 hidden">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Images Queue</label>
                            <div id="preview-grid" class="grid grid-cols-3 gap-2">
                                <!-- Thumbnails loaded dynamically via JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Settings -->
                    <div class="card-premium relative overflow-hidden p-6 rounded-3xl space-y-4">
                        <div class="absolute top-0 left-0 w-16 h-[3px] bg-blue-500"></div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Inventory</h3>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Available Stock Count</label>
                            <div class="flex items-center w-full bg-gray-50 border border-gray-200 rounded-xl overflow-hidden select-none">
                                <button type="button" onclick="adjustStockValue(-1)" class="w-12 h-11 flex items-center justify-center font-extrabold text-gray-600 hover:bg-gray-200/50 hover:text-gray-900 border-r border-gray-200 text-lg transition duration-150">-</button>
                                <input type="number" name="stock" id="product-stock" value="10" min="0" required class="flex-1 text-center bg-transparent border-none outline-none font-black text-sm text-gray-800" style="-moz-appearance: textfield;">
                                <button type="button" onclick="adjustStockValue(1)" class="w-12 h-11 flex items-center justify-center font-extrabold text-gray-600 hover:bg-gray-200/50 hover:text-gray-900 border-l border-gray-200 text-lg transition duration-150">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                        <div id="form-error-msg" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm font-bold rounded-xl px-4 py-3 mb-2"></div>
                        <button type="button" onclick="submitAddProductForm()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl text-base shadow-lg transition transform hover:-translate-y-0.5 block text-center">
                             Save Product
                        </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Client-side JS -->
    <script>
        var bundleOptionIndex = 0;
        var productDescriptionQuill = null;
        var activeVariantCombinations = [];

        function addBundleOptionRow(data) {
            data = data || null;
            var list = document.getElementById('bundle-options-list');
            if (!list) return;
            var row = document.createElement('div');
            row.className = 'p-4 border border-gray-200 bg-gray-50/50 rounded-xl space-y-4 relative bundle-option-row';
            row.setAttribute('data-index', bundleOptionIndex);
            var title = data ? (data.title || '') : '';
            var price = data ? (data.price || '') : '';
            var compare_price = data ? (data.compare_price || '') : '';
            var badge = data ? (data.badge || '') : '';
            var label = data ? (data.label || '') : '';
            var image = data ? (data.image || '') : '';
            var idx = bundleOptionIndex;
            row.innerHTML = '<button type="button" onclick="this.closest(\'.bundle-option-row\').remove()" class="absolute top-2.5 right-2.5 text-gray-400 hover:text-red-500 font-bold text-lg" title="Remove">&times;</button>'
                + '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Option Title</label>'
                + '<input type="text" name="bundle_options[' + idx + '][title]" value="' + title + '" placeholder="e.g. Buy 2 Get 1 Free" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold"></div>'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Savings Badge</label>'
                + '<input type="text" name="bundle_options[' + idx + '][badge]" value="' + badge + '" placeholder="e.g. SAVE RS 500" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold"></div>'
                + '</div>'
                + '<div class="grid grid-cols-3 gap-4">'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Price (Rs.)</label>'
                + '<input type="number" name="bundle_options[' + idx + '][price]" value="' + price + '" placeholder="3500" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold"></div>'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Compare Price (Rs.)</label>'
                + '<input type="number" name="bundle_options[' + idx + '][compare_price]" value="' + compare_price + '" placeholder="4000" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold"></div>'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Ribbon Label</label>'
                + '<input type="text" name="bundle_options[' + idx + '][label]" value="' + label + '" placeholder="SUPER SAVER" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold"></div>'
                + '</div>'
                + '<div><label class="block text-xs font-bold text-gray-500 mb-1">Bundle Image</label>'
                + '<input type="file" name="bundle_options[' + idx + '][image]" accept="image/*" class="w-full text-xs text-gray-500">'
                + (image ? '<input type="hidden" name="bundle_options[' + idx + '][existing_image]" value="' + image + '"><p class="text-xs text-green-600 mt-1">Current: ' + image.split('/').pop() + '</p>' : '')
                + '</div>';
            list.appendChild(row);
            bundleOptionIndex++;
        }

        function toggleBundleFields() {
            var checked = document.getElementById('product-is-bundle').checked;
            var container = document.getElementById('bundle-fields-container');
            if (checked) {
                container.classList.remove('hidden');
                var list = document.getElementById('bundle-options-list');
                if (list && list.children.length === 0) addBundleOptionRow();
            } else {
                container.classList.add('hidden');
            }
        }

        function toggleDiscountFields() {
            var checked = document.getElementById('product-is-discount').checked;
            var container = document.getElementById('discount-fields-container');
            container.classList[checked ? 'remove' : 'add']('hidden');
        }

        function adjustStockValue(amount) {
            var stockInput = document.getElementById('product-stock');
            if (!stockInput) return;
            var val = parseInt(stockInput.value, 10);
            if (isNaN(val)) val = 0;
            val += amount;
            if (val < 0) val = 0;
            stockInput.value = val;
        }

        function getCartesianProduct(arrays) {
            return arrays.reduce(function(acc, curr) {
                var res = [];
                acc.forEach(function(a) { curr.forEach(function(b) { res.push(a.concat([b])); }); });
                return res;
            }, [[]]);
        }

        function parseVariantsText(text) {
            var parsed = {};
            var lines = text.split('\n').map(function(l) { return l.trim(); }).filter(function(l) { return l !== ''; });
            if (!lines.length) return parsed;
            var hasColon = lines.some(function(l) { return l.indexOf(':') !== -1; });
            if (hasColon) {
                lines.forEach(function(line) {
                    var colonIdx = line.indexOf(':');
                    if (colonIdx > 0) {
                        var name = line.substring(0, colonIdx).trim();
                        var vals = line.substring(colonIdx + 1).split(',').map(function(v) { return v.trim(); }).filter(function(v) { return v !== ''; });
                        if (name && vals.length) parsed[name] = vals;
                    }
                });
            } else {
                parsed['Option'] = lines;
            }
            return parsed;
        }

        function rebuildVariantCombinationsTable() {
            var textarea = document.getElementById('product-variants-text');
            var container = document.getElementById('variant-combinations-container');
            var tbody = document.getElementById('variant-combinations-tbody');
            if (!textarea || !container || !tbody) return;

            var parsed = parseVariantsText(textarea.value);
            var keys = Object.keys(parsed);

            if (!keys.length) {
                container.classList.add('hidden');
                document.getElementById('variant-combinations-json').value = '';
                activeVariantCombinations = [];
                tbody.innerHTML = '';
                return;
            }

            container.classList.remove('hidden');
            var combinations = getCartesianProduct(keys.map(function(k) { return parsed[k]; }));

            var priceEl = document.getElementById('product-price');
            var cpEl = document.getElementById('product-compare-price');
            var stockEl = document.getElementById('product-stock');
            var defPrice = priceEl ? priceEl.value : '';
            var defCP = cpEl ? cpEl.value : '';
            var defStock = stockEl ? stockEl.value : '10';

            var oldMap = {};
            activeVariantCombinations.forEach(function(c) { oldMap[c.name] = c; });

            var newCombos = [];
            tbody.innerHTML = '';

            combinations.forEach(function(combo, cidx) {
                var comboName = combo.join(' / ');
                var comboObj = {};
                keys.forEach(function(key, ki) { comboObj[key] = combo[ki]; });

                var old = oldMap[comboName];
                var p = old ? old.price : defPrice;
                var cp = old ? old.compare_price : defCP;
                var st = old ? old.stock : defStock;

                newCombos.push({
                    name: comboName,
                    combination: comboObj,
                    price: (p !== '' && p !== null) ? parseFloat(p) : null,
                    compare_price: (cp !== '' && cp !== null) ? parseFloat(cp) : null,
                    stock: parseInt(st) || 0
                });

                var tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50/50 transition';
                tr.innerHTML = '<td class="py-3 font-bold text-gray-800">' + comboName + '</td>'
                    + '<td class="py-3 pr-2"><input type="number" data-index="' + cidx + '" data-field="price" value="' + (p !== null ? p : '') + '" class="variant-input w-full px-2.5 py-1.5 border border-gray-200 rounded-lg bg-white text-xs font-semibold outline-none focus:border-blue-500"></td>'
                    + '<td class="py-3 pr-2"><input type="number" data-index="' + cidx + '" data-field="compare_price" value="' + (cp !== null ? cp : '') + '" class="variant-input w-full px-2.5 py-1.5 border border-gray-200 rounded-lg bg-white text-xs font-semibold outline-none focus:border-blue-500"></td>'
                    + '<td class="py-3"><input type="number" data-index="' + cidx + '" data-field="stock" value="' + st + '" class="variant-input w-full px-2.5 py-1.5 border border-gray-200 rounded-lg bg-white text-xs font-semibold outline-none focus:border-blue-500"></td>';
                tbody.appendChild(tr);
            });

            activeVariantCombinations = newCombos;
            syncVariantCombinationsJson();

            tbody.querySelectorAll('.variant-input').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    var i = parseInt(e.target.getAttribute('data-index'));
                    var f = e.target.getAttribute('data-field');
                    var v = e.target.value;
                    if (f === 'price' || f === 'compare_price' || f === 'stock') {
                        v = v !== '' ? parseFloat(v) : null;
                    }
                    activeVariantCombinations[i][f] = v;
                    syncVariantCombinationsJson();
                });
            });
        }

        function syncVariantCombinationsJson() {
            var el = document.getElementById('variant-combinations-json');
            if (el) el.value = JSON.stringify(activeVariantCombinations);
        }

        function submitAddProductForm() {
            var errorBox = document.getElementById('form-error-msg');
            errorBox.classList.add('hidden');
            errorBox.textContent = '';

            var nameEl = document.getElementById('product-name-input');
            var priceEl = document.getElementById('product-price');
            var nameVal = nameEl ? nameEl.value.trim() : '';
            var priceVal = priceEl ? priceEl.value.trim() : '';

            if (!nameVal) {
                errorBox.textContent = 'Enter Product Name (Required)';
                errorBox.classList.remove('hidden');
                if (nameEl) nameEl.focus();
                return;
            }
            if (!priceVal || isNaN(parseFloat(priceVal))) {
                errorBox.textContent = 'Enter Sale Price (Required)';
                errorBox.classList.remove('hidden');
                if (priceEl) priceEl.focus();
                return;
            }

            if (productDescriptionQuill) {
                var descEl = document.getElementById('product_description');
                if (descEl) descEl.value = productDescriptionQuill.root.innerHTML;
            }

            var form = document.getElementById('add-product-form');
            if (form) form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            productDescriptionQuill = new Quill('#product-description-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ align: [] }],
                        ['link', 'image', 'video'],
                        ['blockquote', 'code-block'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['clean']
                    ]
                }
            });

            var variantsTA = document.getElementById('product-variants-text');
            if (variantsTA) {
                variantsTA.addEventListener('input', rebuildVariantCombinationsTable);
                var p2 = document.getElementById('product-price');
                var c2 = document.getElementById('product-compare-price');
                var s2 = document.getElementById('product-stock');
                if (p2) p2.addEventListener('input', rebuildVariantCombinationsTable);
                if (c2) c2.addEventListener('input', rebuildVariantCombinationsTable);
                if (s2) s2.addEventListener('input', rebuildVariantCombinationsTable);
            }

            var imgUploader = document.getElementById('images-uploader');
            if (imgUploader) {
                imgUploader.addEventListener('change', function(e) {
                    var grid = document.getElementById('preview-grid');
                    var previewCont = document.getElementById('preview-container');
                    if (!grid || !previewCont) return;
                    grid.innerHTML = '';
                    var files = e.target.files;
                    if (files.length > 0) {
                        previewCont.classList.remove('hidden');
                        Array.from(files).forEach(function(file) {
                            var reader = new FileReader();
                            reader.onload = function(ev) {
                                var d = document.createElement('div');
                                d.className = 'aspect-square rounded-lg overflow-hidden border border-gray-200 bg-white relative';
                                d.innerHTML = '<img src="' + ev.target.result + '" class="w-full h-full object-cover">';
                                grid.appendChild(d);
                            };
                            reader.readAsDataURL(file);
                        });
                    } else {
                        previewCont.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>
