<!-- Backdrop -->
<div id="cartBackdrop" onclick="closeCartDrawer()" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[998] transition-opacity duration-300 opacity-0 pointer-events-none"></div>

<!-- Off-Canvas Cart Drawer -->
<div id="cartDrawer" class="fixed top-0 bottom-0 right-0 w-full sm:w-[430px] bg-white z-[999] shadow-2xl transition-transform duration-300 transform translate-x-full border-l border-gray-100 flex flex-col">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
        <h3 class="font-black text-gray-900 text-xl tracking-tight">Cart • <span id="cart-item-count">0</span> items</h3>
        <button onclick="closeCartDrawer()" class="text-gray-400 hover:text-red-500 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Green Reservation Banner -->
    <div class="bg-[#4c9b0f] text-white py-2.5 px-4 text-center font-bold text-sm tracking-wide shadow-inner flex items-center justify-center gap-1.5 shrink-0 select-none">
        <span>{{ $settings->enable_rtl ? 'آپ کا کارٹ محفوظ ہے:' : 'Cart reserved for' }}</span>
        <span id="cart-timer-countdown" class="font-black">05:00</span>
    </div>

    <!-- Free Shipping Progress Bar Card -->
    <div class="p-5 bg-white border-b border-gray-100 space-y-2.5 shrink-0 select-none">
        <p id="free-shipping-message" class="text-center text-sm font-black text-gray-800 leading-snug">{{ $settings->enable_rtl ? 'مبارک ہو! آپ کو مفت ڈیلیوری مل گئی ہے!' : 'Congrats! You get FREE shipping!' }}</p>
        <div class="relative w-full h-4 bg-gray-100 rounded-full border border-gray-200 shadow-inner overflow-visible">
            <div id="free-shipping-bar" class="h-full rounded-full transition-all duration-500" style="width: 100%; background: repeating-linear-gradient(45deg, #4c9b0f, #4c9b0f 10px, #5fa221 10px, #5fa221 20px);"></div>
            <!-- Floating Truck Icon -->
            <div id="free-shipping-truck" class="absolute -top-1 w-6 h-6 rounded-full bg-white border border-gray-200 shadow flex items-center justify-center text-xs transition-all duration-500" style="left: calc(100% - 12px);">
                🚚
            </div>
        </div>
    </div>

    <!-- Scrollable Items List -->
    <div id="cartItemsList" class="flex-grow p-6 overflow-y-auto space-y-4 bg-white custom-scrollbar">
        <!-- Rendered in JS -->
    </div>

    <!-- Sticky Footer Summary Container -->
    <div class="shrink-0 border-t border-gray-150 bg-white">
        <!-- Enter discount code field -->
        <div class="p-4 bg-white border-b border-gray-100 select-none">
            <div class="flex gap-2.5">
                <input type="text" placeholder="Enter discount code" class="flex-grow px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-xs font-semibold outline-none focus:border-[#4c9b0f] transition">
                <button class="bg-[#4c9b0f] hover:bg-[#3d7c0c] text-white font-black px-6 py-2.5 rounded-xl text-xs transition uppercase">ADD</button>
            </div>
        </div>

        <!-- Savings & Subtotal summary -->
        <div class="p-4 bg-white space-y-2.5 font-sans select-none">
            <div id="cart-savings-row" class="flex justify-between items-center text-sm font-bold text-gray-500" style="display: none;">
                <span>Savings</span>
                <span id="cartTotalSavings" class="text-sm font-black text-green-600">-Rs. 0.00</span>
            </div>
            <div class="flex justify-between items-center text-base font-bold text-gray-900">
                <span class="text-gray-950 font-black">Subtotal</span>
                <span id="cartSubtotal" class="text-xl font-black text-gray-950">Rs. 0.00</span>
            </div>
        </div>

        <!-- Stacked action buttons with shortcuts -->
        <div class="p-4 bg-white border-t border-gray-100 space-y-3 shrink-0">
            <!-- Row 1: COD + Phone -->
            <div class="flex gap-3">
                <a href="/checkout" style="background-color: {{ $settings->btn_buy_now_bg ?? '#84cc16' }}; color: {{ $settings->btn_buy_now_text_color ?? '#ffffff' }};" class="flex-grow text-center font-black py-4 rounded-xl text-sm shadow-lg hover:opacity-90 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>{{ $settings->btn_buy_now_text ?? 'Order Now - Cash on Delivery' }}</span>
                </a>
                @if(!empty($settings->footer_phone))
                    <a href="tel:{{ $settings->footer_phone }}" class="w-14 h-14 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg flex items-center justify-center shrink-0 transition transform hover:-translate-y-0.5" title="Call Us">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </a>
                @endif
            </div>
            
            <!-- Row 2: Checkout + WhatsApp -->
            <div class="flex gap-3">
                <a href="/checkout" style="background-color: {{ $settings->btn_add_to_cart_bg ?? '#16a34a' }}; color: {{ $settings->btn_add_to_cart_text_color ?? '#ffffff' }};" class="flex-grow text-center font-black py-4 rounded-xl text-sm shadow-lg hover:opacity-90 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span>Check out</span>
                </a>
                @if(!empty($settings->footer_phone) || !empty($settings->footer_whatsapp))
                    @php
                        $phoneNum = preg_replace('/[^0-9]/', '', $settings->footer_whatsapp ?? $settings->footer_phone ?? '');
                    @endphp
                    <a href="https://wa.me/{{ $phoneNum }}?text=Hello,%20I%20want%20to%20inquire%20about%20my%20cart!" target="_blank" class="w-14 h-14 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg flex items-center justify-center shrink-0 transition transform hover:-translate-y-0.5" title="WhatsApp Us">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.455 5.703 1.456h.008c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    const isRtl = {{ $settings->enable_rtl ? 'true' : 'false' }};
    let globalCart = [];
    let timeLeft = localStorage.getItem('cart_timer_left');
    if (!timeLeft || timeLeft <= 0) {
        timeLeft = 300; // 5 minutes default
    }

    // Load cart on DOM load
    document.addEventListener('DOMContentLoaded', () => {
        try {
            globalCart = JSON.parse(localStorage.getItem('cart') || '[]');
        } catch(e) {
            globalCart = [];
        }
        updateCartBadge();
        startCartTimer();
    });

    function startCartTimer() {
        const timerEl = document.getElementById('cart-timer-countdown');
        if (!timerEl) return;
        
        // Reset countdown timer
        const interval = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                timeLeft = 300;
            }
            localStorage.setItem('cart_timer_left', timeLeft);
            
            const mins = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerEl.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function openCartDrawer() {
        renderCartItems();
        document.getElementById('cartBackdrop').classList.remove('opacity-0', 'pointer-events-none');
        document.getElementById('cartBackdrop').classList.add('opacity-100');
        document.getElementById('cartDrawer').classList.remove('translate-x-full');
    }

    function closeCartDrawer() {
        document.getElementById('cartBackdrop').classList.remove('opacity-100');
        document.getElementById('cartBackdrop').classList.add('opacity-0', 'pointer-events-none');
        document.getElementById('cartDrawer').classList.add('translate-x-full');
    }

    function updateCartBadge() {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        
        let count = 0;
        globalCart.forEach(item => count += (item.qty || 1));
        badge.innerText = count;
        
        // Show/hide badge
        if (count === 0) {
            badge.style.display = 'none';
        } else {
            badge.style.display = 'flex';
        }
    }

    function addToCart(id, name, price, image, qty = 1, forceRedirect = false, selectedVariants = {}, originalPrice = null) {
        qty = parseInt(qty);
        let existing = globalCart.find(item => 
            item.id == id && 
            JSON.stringify(item.selectedVariants || {}) === JSON.stringify(selectedVariants || {})
        );

        if (existing) {
            existing.qty += qty;
        } else {
            globalCart.push({
                id: id,
                name: name,
                price: parseFloat(price),
                originalPrice: parseFloat(originalPrice || price),
                image: image,
                qty: qty,
                selectedVariants: selectedVariants
            });
        }

        localStorage.setItem('cart', JSON.stringify(globalCart));
        updateCartBadge();

        if (forceRedirect) {
            window.location.href = '/checkout';
        } else {
            openCartDrawer();
        }
    }

    function updateCartQty(index, direction) {
        globalCart[index].qty += direction;
        
        if (globalCart[index].qty <= 0) {
            globalCart.splice(index, 1);
        }

        localStorage.setItem('cart', JSON.stringify(globalCart));
        updateCartBadge();
        renderCartItems();
    }

    function deleteCartItem(index) {
        globalCart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(globalCart));
        updateCartBadge();
        renderCartItems();
    }

    function renderCartItems() {
        const container = document.getElementById('cartItemsList');
        const subtotalEl = document.getElementById('cartSubtotal');
        const itemCountEl = document.getElementById('cart-item-count');
        const savingsRow = document.getElementById('cart-savings-row');
        const totalSavingsEl = document.getElementById('cartTotalSavings');
        
        const freeShippingMessage = document.getElementById('free-shipping-message');
        const freeShippingBar = document.getElementById('free-shipping-bar');
        const freeShippingTruck = document.getElementById('free-shipping-truck');

        if (!container) return;

        container.innerHTML = '';
        let subtotal = 0;
        let totalSavings = 0;
        let count = 0;

        globalCart.forEach(item => count += item.qty);
        if (itemCountEl) itemCountEl.innerText = count;

        if (globalCart.length === 0) {
            const emptyText = isRtl ? 'آپ کا کارٹ ابھی خالی ہے۔' : 'Your cart is empty.';
            const viewProductsText = isRtl ? 'پروڈکٹس دیکھیں' : 'Browse Products';
            container.innerHTML = `
                <div class="text-center py-16 text-gray-400 font-bold space-y-3 select-none">
                    <span class="text-6xl block">🛒</span>
                    <p class="text-sm">${emptyText}</p>
                    <a href="/collection" class="inline-block bg-green-50 text-green-700 border border-green-200 text-xs px-4 py-2.5 rounded-xl hover:bg-green-100 transition font-black font-sans">${viewProductsText}</a>
                </div>
            `;
            subtotalEl.innerText = 'Rs. 0.00';
            if (savingsRow) savingsRow.style.display = 'none';
            
            // Progress Bar resets
            if (freeShippingMessage) {
                freeShippingMessage.innerText = isRtl 
                    ? 'مفت ڈیلیوری حاصل کرنے کے لیے مزید Rs. 2,000.00 کا آرڈر کریں!' 
                    : 'Spend Rs. 2,000.00 more for FREE shipping!';
            }
            if (freeShippingBar) freeShippingBar.style.width = '0%';
            if (freeShippingTruck) freeShippingTruck.style.left = 'calc(0% - 12px)';
            return;
        }

        globalCart.forEach((item, index) => {
            subtotal += item.price * item.qty;
            if (item.originalPrice && item.originalPrice > item.price) {
                totalSavings += (item.originalPrice - item.price) * item.qty;
            }

            const card = document.createElement('div');
            card.className = 'flex gap-4 bg-gray-50 p-4 border border-gray-150 rounded-[1.5rem] relative shadow-sm hover:border-[#4c9b0f] transition duration-300';
            
            const imageHtml = item.image 
                ? `<img src="${item.image.startsWith('http') || item.image.startsWith('/') ? item.image : '/storage/' + item.image}" class="w-20 h-20 rounded-2xl object-cover border border-gray-150 bg-white">`
                : `<div class="w-20 h-20 bg-white rounded-2xl border border-gray-150 flex items-center justify-center text-[10px] text-gray-400 font-bold">No Image</div>`;

            let variantsHtml = '';
            if (item.selectedVariants && Object.keys(item.selectedVariants).length > 0) {
                variantsHtml = '<div class="text-[10px] text-gray-500 font-bold mt-1.5 flex flex-wrap gap-1">';
                for (let [key, val] of Object.entries(item.selectedVariants)) {
                    variantsHtml += `<span class="inline-block bg-gray-200/70 text-gray-700 px-2 py-0.5 rounded">${key}: ${val}</span>`;
                }
                variantsHtml += '</div>';
            }

            // Stacks of price labels
            let compareHtml = '';
            let savingHtml = '';
            let sellingHtml = `<span class="text-base font-black text-[#4c9b0f] block text-right">Rs. ${(item.price * item.qty).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;

            if (item.originalPrice && item.originalPrice > item.price) {
                const totalOrig = item.originalPrice * item.qty;
                const totalSav = (item.originalPrice - item.price) * item.qty;
                compareHtml = `<span class="text-xs font-bold text-red-500 line-through block text-right">Rs. ${totalOrig.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                savingHtml = `<span class="text-[10px] font-bold text-gray-700 block text-right">(You save Rs. ${totalSav.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})})</span>`;
            }

            card.innerHTML = `
                ${imageHtml}
                <div class="flex-grow flex flex-col justify-between">
                    <div>
                        <h4 class="font-extrabold text-gray-900 text-sm pr-5 leading-tight">${item.name}</h4>
                        ${variantsHtml}
                    </div>
                    <div class="flex items-center justify-between mt-3 select-none">
                        <!-- Qty Selector -->
                        <div class="flex items-center gap-1 bg-gray-100 p-0.5 rounded-lg border border-gray-200">
                            <button type="button" onclick="updateCartQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white border border-gray-150 rounded-md text-xs font-bold hover:bg-gray-150 transition">-</button>
                            <span class="text-xs font-black w-6 text-center">${item.qty}</span>
                            <button type="button" onclick="updateCartQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-white border border-gray-150 rounded-md text-xs font-bold hover:bg-gray-150 transition">+</button>
                        </div>
                        <!-- Prices Stack -->
                        <div class="text-right leading-tight font-sans">
                            ${compareHtml}
                            ${sellingHtml}
                            ${savingHtml}
                        </div>
                    </div>
                </div>
                <!-- Trash Delete button -->
                <button type="button" onclick="deleteCartItem(${index})" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition" title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(card);
        });

        // Set subtotal
        subtotalEl.innerText = 'Rs. ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        // Set savings row
        if (savingsRow && totalSavingsEl) {
            if (totalSavings > 0) {
                savingsRow.style.display = 'flex';
                totalSavingsEl.innerText = `-Rs. ${totalSavings.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            } else {
                savingsRow.style.display = 'none';
            }
        }

        // Set dynamic shipping progress bar (threshold = Rs. 2,000)
        const threshold = 2000;
        if (freeShippingMessage && freeShippingBar && freeShippingTruck) {
            if (subtotal >= threshold) {
                freeShippingMessage.innerText = isRtl 
                    ? 'مبارک ہو! آپ کو مفت ڈیلیوری مل گئی ہے! 🎉' 
                    : 'Congrats! You get FREE shipping! 🎉';
                freeShippingBar.style.width = '100%';
                freeShippingTruck.style.left = 'calc(100% - 12px)';
            } else {
                const needed = threshold - subtotal;
                const formattedNeeded = needed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                freeShippingMessage.innerText = isRtl 
                    ? `مفت ڈیلیوری حاصل کرنے کے لیے مزید Rs. ${formattedNeeded} کا آرڈر کریں!` 
                    : `Spend Rs. ${formattedNeeded} more for FREE shipping!`;
                const pct = Math.min(100, (subtotal / threshold) * 100);
                freeShippingBar.style.width = `${pct}%`;
                freeShippingTruck.style.left = `calc(${pct}% - 12px)`;
            }
        }
    }
</script>
