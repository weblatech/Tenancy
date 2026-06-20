$filePath = "resources/views/tenant/add-product.blade.php"
$lines = Get-Content $filePath
$header = $lines[0..283]  # lines 1-284 (0-indexed: 0-283)

$newScript = @'
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
                errorBox.textContent = 'Product Name درج کریں (ضروری ہے)';
                errorBox.classList.remove('hidden');
                if (nameEl) nameEl.focus();
                return;
            }
            if (!priceVal || isNaN(parseFloat(priceVal))) {
                errorBox.textContent = 'Sale Price درج کریں (ضروری ہے)';
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
'@

$result = $header + ($newScript -split "`n")
Set-Content $filePath $result -Encoding UTF8
Write-Output "Done. Lines: $($result.Length)"
