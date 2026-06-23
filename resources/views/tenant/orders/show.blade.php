<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail #{{ $order->id }} - {{ strtoupper($tenantId) }} Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Leaflet JS Map integration -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Order Details</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop/orders" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Back to Orders</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="relative z-10 max-w-5xl mx-auto mt-10 px-6 space-y-8">
        
        <!-- Banner Alert / Success Toast -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">✅</span>
                <span class="text-xs font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Main Order Header -->
        <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="absolute top-0 left-0 w-32 h-[4px] bg-emerald-500"></div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black text-slate-900">Order #{{ $order->id }}</h1>
                    @php
                        $badgeBg = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                        if ($order->status === 'processing') $badgeBg = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        elseif ($order->status === 'completed') $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        elseif ($order->status === 'cancelled') $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
                    @endphp
                    <span class="px-3.5 py-1 text-[10px] font-black rounded-full border {{ $badgeBg }} uppercase tracking-wider">
                        {{ $order->status }}
                    </span>
                </div>
                <p class="text-slate-400 font-bold text-xs mt-1" style="font-family: sans-serif;">
                    Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <!-- Status Update Form -->
                <form action="/shop/orders/{{ $order->id }}/status" method="POST" class="flex items-center gap-3">
                    @csrf
                    <label class="text-xs font-black text-slate-500 uppercase tracking-wider shrink-0">Fulfillment Status:</label>
                    <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 border border-slate-250 rounded-xl bg-slate-50 text-xs font-extrabold outline-none focus:border-indigo-600 transition">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>

                <!-- Permanent Delete Form -->
                <form action="/shop/orders/{{ $order->id }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this order? This action cannot be undone.')">
                    @csrf
                    <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-250 font-black py-2.5 px-4 rounded-xl text-xs transition duration-150 shadow-sm flex items-center justify-center gap-1.5 shrink-0">
                        🗑️ <span>Delete Order</span>
                    </button>
                </form>
            </div>
        </div>

        @if($order->payment_method === 'cod' && $order->cod_advance_required > 0)
            <div class="p-6 rounded-3xl border flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-md
                @if($order->cod_advance_paid)
                    bg-emerald-50 border-emerald-200 text-emerald-950
                @else
                    bg-rose-50 border-rose-200 text-rose-950
                @endif
            ">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 font-black text-sm md:text-base">
                        @if($order->cod_advance_paid)
                            <span class="text-lg">✅</span>
                            <span>COD Advance Payment Verified (پیشگی ادائیگی موصول ہو گئی ہے)</span>
                        @else
                            <span class="text-lg">⚠️</span>
                            <span>Awaiting COD Advance Payment (پیشگی ادائیگی کا انتظار ہے)</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-slate-500 leading-normal">
                        Advance Payment Required: <span class="font-black text-slate-900" style="font-family: sans-serif;">Rs. {{ number_format($order->cod_advance_required) }}</span>
                        &nbsp;|&nbsp;
                        Doorstep Delivery COD Balance: <span class="font-black text-slate-905" style="font-family: sans-serif;">Rs. {{ number_format($order->total - $order->cod_advance_required) }}</span>
                    </p>
                    @if(!$order->cod_advance_paid)
                        <p class="text-[10px] font-bold text-rose-700 mt-1 leading-normal">
                            📢 Order cannot be moved to Processing or Completed status until the advance payment is marked as verified!
                        </p>
                    @endif
                </div>
                <form action="/shop/orders/{{ $order->id }}/toggle-advance" method="POST" class="shrink-0 w-full md:w-auto">
                    @csrf
                    <button type="submit" class="w-full px-5 py-3 rounded-2xl text-xs font-black shadow-sm transition duration-150 border
                        @if($order->cod_advance_paid)
                            bg-white hover:bg-slate-50 text-slate-700 border-slate-300
                        @else
                            bg-rose-600 hover:bg-rose-700 text-white border-rose-700 shadow-rose-500/10
                        @endif
                    ">
                        @if($order->cod_advance_paid)
                            Mark as Unpaid (کینسل کریں)
                        @else
                            Mark as Paid & Verify (موصول ہو گئی)
                        @endif
                    </button>
                </form>
            </div>
        @endif

        <!-- Grid Layout (Details columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Items list (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8">
                    <div class="absolute top-0 left-0 w-32 h-[4px] bg-emerald-500"></div>
                    <h2 class="text-lg font-black text-slate-900 mb-6">Ordered Items</h2>
                    <div class="border border-slate-150 rounded-2xl overflow-hidden bg-slate-50/20">
                        <table class="w-full text-xs font-semibold">
                            <thead class="bg-slate-50/70 text-slate-500 font-extrabold uppercase tracking-wider border-b border-slate-150">
                                <tr>
                                    <th class="px-5 py-3.5 text-left">Product</th>
                                    <th class="px-5 py-3.5 text-center">Price</th>
                                    <th class="px-5 py-3.5 text-center">Qty</th>
                                    <th class="px-5 py-3.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                @foreach($order->cart_items as $item)
                                    <tr>
                                        <td class="px-5 py-4 text-left font-black text-slate-900">
                                            <div class="flex items-center gap-3">
                                                @if(!empty($item['image']))
                                                    <img src="{{ str_starts_with($item['image'], 'http') || str_starts_with($item['image'], '/') ? $item['image'] : tenant_asset($item['image']) }}" class="w-12 h-12 rounded-xl object-cover border bg-white shadow-sm">
                                                @else
                                                    <div class="w-12 h-12 bg-white rounded-xl border flex items-center justify-center text-[9px] text-slate-400 font-bold shrink-0 shadow-sm">No Image</div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span>{{ $item['name'] }}</span>
                                                    @if(!empty($item['selectedVariants']) && is_array($item['selectedVariants']))
                                                        <div class="flex flex-wrap gap-1 mt-1 text-[10px] text-slate-500 font-bold">
                                                            @foreach($item['selectedVariants'] as $optKey => $optVal)
                                                                <span class="inline-block bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded">{{ $optKey }}: {{ $optVal }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-500" style="font-family: sans-serif;">
                                            Rs. {{ number_format($item['originalPrice'] ?? $item['price'] ?? 0) }}
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-500 font-bold" style="font-family: sans-serif;">
                                            {{ $item['qty'] }}
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-slate-950" style="font-family: sans-serif;">
                                            Rs. {{ number_format(($item['originalPrice'] ?? $item['price'] ?? 0) * $item['qty']) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Summary rows -->
                                @php
                                    $origSubtotal = 0;
                                    foreach($order->cart_items as $item) {
                                        $origPrice = (float)($item['originalPrice'] ?? $item['price'] ?? 0);
                                        $origSubtotal += $origPrice * (int)($item['qty'] ?? 1);
                                    }
                                    $discountAmount = $origSubtotal - $order->subtotal;
                                @endphp
                                <tr class="bg-slate-50/20 font-bold text-slate-500 border-t border-slate-150">
                                    <td colspan="3" class="px-5 py-3 text-left">Original Subtotal</td>
                                    <td class="px-5 py-3 text-right font-black text-slate-950" style="font-family: sans-serif;">Rs. {{ number_format($origSubtotal) }}</td>
                                </tr>
                                @if($discountAmount > 0)
                                    <tr class="bg-slate-50/20 font-bold text-rose-600">
                                        <td colspan="3" class="px-5 py-3 text-left">Special Discount</td>
                                        <td class="px-5 py-3 text-right font-black" style="font-family: sans-serif;">- Rs. {{ number_format($discountAmount) }}</td>
                                    </tr>
                                @endif
                                <tr class="bg-slate-50/20 font-bold text-slate-500">
                                    <td colspan="3" class="px-5 py-3 text-left">Subtotal</td>
                                    <td class="px-5 py-3 text-right font-black text-slate-950" style="font-family: sans-serif;">Rs. {{ number_format($order->subtotal) }}</td>
                                </tr>
                                <tr class="bg-slate-50/20 font-bold text-slate-500">
                                    <td colspan="3" class="px-5 py-3 text-left">Shipping Fee</td>
                                    <td class="px-5 py-3 text-right font-black text-slate-950" style="font-family: sans-serif;">Rs. {{ number_format($order->shipping_fee) }}</td>
                                </tr>
                                <tr class="bg-emerald-50/20 font-black text-emerald-800 border-t border-slate-200">
                                    <td colspan="3" class="px-5 py-4 text-left text-sm uppercase">Grand Total (کل رقم)</td>
                                    <td class="px-5 py-4 text-right text-base text-emerald-600 font-black" style="font-family: sans-serif;">Rs. {{ number_format($order->total) }}</td>
                                </tr>
                                @if($order->payment_method === 'cod' && $order->cod_advance_required > 0)
                                <tr class="bg-amber-50/20 font-bold text-amber-900 border-t border-amber-250">
                                    <td colspan="3" class="px-5 py-3 text-left">COD Advance Required</td>
                                    <td class="px-5 py-3 text-right font-black text-amber-700" style="font-family: sans-serif;">Rs. {{ number_format($order->cod_advance_required) }}</td>
                                </tr>
                                <tr class="bg-slate-50/20 font-bold text-slate-700 border-t border-slate-200">
                                    <td colspan="3" class="px-5 py-3 text-left">Remaining COD Balance</td>
                                    <td class="px-5 py-3 text-right font-black text-slate-800" style="font-family: sans-serif;">Rs. {{ number_format($order->total - $order->cod_advance_required) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shopify Right Sidebar Cards -->
            <div class="space-y-6">
                
                <!-- Card 1: IP Address -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 space-y-3">
                    <div class="absolute top-0 left-0 w-16 h-[3px] bg-emerald-500"></div>
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">IP Address</h2>
                    <span class="text-sm font-black text-slate-900 block" style="font-family: sans-serif;">
                        {{ $order->ip_address ?? '127.0.0.1' }}
                    </span>
                </div>

                <!-- Channel card removed at user request -->

                <!-- Card 3: Customer Profile (Exactly like Shopify mockup) -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 space-y-5">
                    <div class="absolute top-0 left-0 w-16 h-[3px] bg-emerald-500"></div>
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Customer</h2>
                        <span class="text-slate-400 hover:text-slate-600 cursor-pointer text-sm">•••</span>
                    </div>
                    
                    <div class="space-y-4 text-xs font-bold text-slate-700">
                        <div>
                            <a href="#" class="text-sm font-black text-blue-600 hover:underline block">{{ $order->customer_name }}</a>
                            <span class="text-[10px] text-slate-400 block mt-0.5 font-bold">1 order</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block mb-1">Contact information</span>
                            <span class="text-slate-500 font-semibold block mb-0.5">No email provided</span>
                            <span class="text-slate-900 block font-semibold" style="font-family: sans-serif;">{{ $order->customer_phone }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block mb-1">Shipping address</span>
                            <div class="text-slate-500 font-semibold leading-relaxed">
                                {{ $order->customer_name }}<br>
                                {{ $order->customer_address }}<br>
                                {{ $order->customer_city }}<br>
                                {{ $order->ip_country ?? 'Pakistan' }}<br>
                                <span style="font-family: sans-serif;">{{ $order->customer_phone }}</span>
                            </div>
                            @php
                                $mapQuery = !empty($order->latitude) && !empty($order->longitude) 
                                    ? "{$order->latitude},{$order->longitude}" 
                                    : urlencode($order->customer_address . ', ' . $order->customer_city);
                                $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$mapQuery}";
                            @endphp
                            <a href="{{ $googleMapsUrl }}" target="_blank" class="text-blue-600 hover:underline text-xs font-black block mt-2">View map</a>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block mb-1">Billing address</span>
                            <span class="text-slate-500 font-semibold block">Same as shipping address</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold block mb-1">Payment Method</span>
                            <span class="text-slate-950 block font-black uppercase text-xs">{{ $order->payment_method ?? 'COD' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Conversion Summary -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 space-y-3">
                    <div class="absolute top-0 left-0 w-16 h-[3px] bg-emerald-500"></div>
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Conversion summary</h2>
                    <div class="text-xs text-slate-500 font-semibold leading-relaxed">
                        There aren't any conversion details available for this order.
                    </div>
                    <a href="https://help.shopify.com" target="_blank" class="text-blue-600 hover:underline text-xs font-black block mt-1">Learn more</a>
                </div>

                <!-- Card 5: WhatsApp Communication -->
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                    if (str_starts_with($cleanPhone, '03')) {
                        $cleanPhone = '92' . substr($cleanPhone, 1);
                    }
                    
                    // Build item details list for WhatsApp text
                    $itemsList = "";
                    if (is_array($order->cart_items)) {
                        foreach ($order->cart_items as $item) {
                            $itemName = $item['name'] ?? 'Product';
                            $itemQty = $item['qty'] ?? 1;
                            $itemPrice = number_format(($item['originalPrice'] ?? $item['price']) * $itemQty);
                            $itemsList .= "- {$itemName} x {$itemQty} (Rs. {$itemPrice})\n";
                        }
                    }
                    
                    $storeName = strtoupper($tenantId);
                    $orderDate = $order->created_at ? $order->created_at->format('Y-m-d H:i') : date('Y-m-d H:i');
                    
                    $advanceText = "";
                    if ($order->payment_method === 'cod' && $order->cod_advance_required > 0) {
                        $advanceText = "*COD Advance / ایڈوانس پیمنٹ:* *Rs. " . number_format($order->cod_advance_required) . "* (" . ($order->cod_advance_paid ? 'Paid/Verified' : 'Pending') . ")\n"
                                     . "*Remaining COD / بقیہ رقم:* *Rs. " . number_format($order->total - $order->cod_advance_required) . "*\n"
                                     . "-----------------------------------\n";
                    }
                    
                    // Professional bilingual WhatsApp Message template
                    $messageText = "*ORDER CONFIRMATION / آرڈر کی تصدیق* 🛍️\n"
                                 . "-----------------------------------\n"
                                 . "Dear *{$order->customer_name}*,\n"
                                 . "Thank you for shopping with *{$storeName}*! Your order has been received.\n\n"
                                 . "پیارے *{$order->customer_name}*,\n"
                                 . "*{$storeName}* سے خریداری کرنے کا شکریہ! آپ کا آرڈر موصول ہو گیا ہے۔\n\n"
                                 . "*Order ID / آرڈر نمبر:* #{$order->id}\n"
                                 . "*Date / تاریخ:* {$orderDate}\n"
                                 . "-----------------------------------\n"
                                 . "*Order Items / آرڈر کی تفصیل:*\n"
                                 . $itemsList
                                 . "-----------------------------------\n"
                                 . "*Subtotal / رقم:* Rs. " . number_format($order->subtotal) . "\n"
                                 . "*Shipping / ڈیلیوری:* Rs. " . number_format($order->shipping_fee) . "\n"
                                 . "*Total Amount / کل رقم:* *Rs. " . number_format($order->total) . "*\n"
                                 . "-----------------------------------\n"
                                 . $advanceText
                                 . "*Shipping Address / شپنگ ایڈریس:*\n"
                                 . "*Name:* {$order->customer_name}\n"
                                 . "*Phone:* {$order->customer_phone}\n"
                                 . "*Address:* {$order->customer_address}, {$order->customer_city}\n"
                                 . "-----------------------------------\n"
                                 . "We will process your order soon.\n"
                                 . "ہم جلد ہی آپ کا آرڈر روانہ کریں گے۔";

                    $waLink = "https://wa.me/{$cleanPhone}?text=" . urlencode($messageText);
                @endphp
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 space-y-4">
                    <div class="absolute top-0 left-0 w-16 h-[3px] bg-emerald-500"></div>
                    <h2 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span class="text-emerald-500">💬</span>
                        <span>WhatsApp Actions</span>
                    </h2>
                    <p class="text-xs font-medium text-slate-500 leading-relaxed">
                        Send a detailed, bilingual order confirmation message directly to this customer's WhatsApp number.
                    </p>
                    <a href="{{ $waLink }}" target="_blank" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/35 transition-all transform hover:-translate-y-0.5 duration-200 flex items-center justify-center gap-2.5 text-sm">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-3.376c1.614.957 3.524 1.463 5.485 1.464 5.769 0 10.463-4.69 10.467-10.46 0-2.8-.1.087-5.419-4.832-1.96-1.957-4.566-3.036-7.34-3.037-5.772 0-10.471 4.693-10.475 10.463-.001 1.955.51 3.86 1.478 5.498l-.979 3.579 3.673-.963zm11.593-5.26c-.302-.151-1.787-.881-2.062-.981-.275-.1-.475-.151-.675.151-.2.302-.775.981-.95 1.18-.175.2-.35.225-.65.075-.302-.151-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.675-2.085-.175-.302-.019-.465.132-.614.135-.133.302-.352.453-.528.151-.175.2-.302.302-.503.1-.2.05-.377-.025-.528-.075-.151-.675-1.628-.925-2.229-.244-.588-.491-.508-.675-.518-.174-.01-.375-.012-.575-.012-.2 0-.525.075-.8.376-.275.302-1.05 1.03-1.05 2.512 0 1.48 1.075 2.912 1.225 3.112.15.2 2.115 3.23 5.123 4.527.715.308 1.273.493 1.708.631.714.227 1.365.195 1.879.119.573-.086 1.787-.73 2.037-1.432.25-.702.25-1.303.175-1.43-.075-.128-.275-.203-.575-.353z"/>
                        </svg>
                        <span>WhatsApp Confirmation</span>
                    </a>
                </div>

                <!-- Geolocation & IP Info Card -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 space-y-4">
                    <div class="absolute top-0 left-0 w-16 h-[3px] bg-emerald-500"></div>
                    <h2 class="text-base font-black text-slate-900 pb-3 border-b border-slate-100 uppercase tracking-wider flex items-center gap-2">
                        <span>🌐</span>
                        <span>Location & IP Info</span>
                    </h2>
                    
                    <div class="space-y-4 text-xs font-bold text-slate-700">
                        @if(!empty($order->ip_address))
                            <div class="flex items-center justify-between bg-slate-50 p-3 rounded-2xl border border-slate-150">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-0.5">IP Address</span>
                                    <span class="text-xs font-black text-slate-950 block" style="font-family: sans-serif;">{{ $order->ip_address }}</span>
                                </div>
                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-150 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider">{{ $order->isp ?? 'PTCL' }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-0.5">City</span>
                                    <span class="text-sm font-black text-slate-950 block">{{ $order->ip_city ?? $order->customer_city }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-0.5">Country</span>
                                    <span class="text-sm font-black text-slate-950 block">{{ $order->ip_country ?? 'Pakistan' }}</span>
                                </div>
                            </div>

                            @if(!empty($order->latitude) && !empty($order->longitude))
                                <div class="pt-2">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider block mb-1">Live Order Map</span>
                                    <div id="live-map" class="h-44 rounded-xl border border-slate-200 shadow-sm z-10 relative bg-slate-100"></div>
                                    <div class="text-[9px] text-slate-400 font-bold mt-1 text-center" style="font-family: sans-serif;">
                                        Coordinates: {{ $order->latitude }}, {{ $order->longitude }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-6 text-slate-400">
                                <span class="text-3xl block mb-2">📡</span>
                                No network/IP metadata available for this order.
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Leaflet JS Map script initialization -->
    @if(!empty($order->ip_address) && !empty($order->latitude) && !empty($order->longitude))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lat = parseFloat("{{ $order->latitude }}");
            const lon = parseFloat("{{ $order->longitude }}");
            
            // Initialize Leaflet Map
            const map = L.map('live-map', {
                zoomControl: true,
                dragging: true,
                touchZoom: true,
                scrollWheelZoom: false
            }).setView([lat, lon], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([lat, lon]).addTo(map);
            marker.bindPopup("<b>{{ $order->customer_name }}</b><br>{{ $order->ip_city ?? $order->customer_city }}, {{ $order->ip_country ?? 'PK' }}").openPopup();
        });
    </script>
    @endif
</body>
</html>
