@php
    // Today's Date Info
    $todayDate = now()->format('M d, Y');

    // Filter today's orders
    $todayOrders = $orders->filter(fn($o) => $o->created_at->isToday());
    
    // Today's metrics
    $todayOrdersCount = $todayOrders->count();
    $todayItemsCount = $todayOrders->sum(function($o) {
        return is_array($o->cart_items) ? collect($o->cart_items)->sum('qty') : 0;
    });
    $todayFulfilledCount = $todayOrders->filter(fn($o) => in_array($o->status, ['processing', 'completed']))->count();
    $todayDeliveredCount = $todayOrders->filter(fn($o) => $o->status === 'completed')->count();
    $todayReturnsAmount = 0; // Static placeholder PKR 0

    // All Time metrics
    $allTimeOrdersCount = $orders->count();
    $allTimeItemsCount = $orders->sum(function($o) {
        return is_array($o->cart_items) ? collect($o->cart_items)->sum('qty') : 0;
    });
    $allTimeFulfilledCount = $orders->filter(fn($o) => in_array($o->status, ['processing', 'completed']))->count();
    $allTimeDeliveredCount = $orders->filter(fn($o) => $o->status === 'completed')->count();
    $allTimeReturnsAmount = 0; // Static placeholder PKR 0
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Orders Manager</title>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Orders Manager</span>
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

        <!-- Shopify-Style Metrics Ribbon -->
        <div class="card-premium relative overflow-hidden rounded-3xl shadow-sm mb-8">
            <div class="flex min-w-[768px] md:min-w-0 md:grid md:grid-cols-6 divide-x divide-slate-150">
                
                <!-- Timeframe Selector -->
                <div class="p-5 flex items-center justify-between bg-slate-50/40 select-none">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200 text-slate-500 shadow-sm">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Timeframe</span>
                            <div class="relative inline-flex items-center gap-1 mt-0.5">
                                <select id="timeframe-select" class="text-xs font-black text-slate-800 bg-transparent border-none p-0 pr-4 focus:ring-0 cursor-pointer outline-none appearance-none">
                                    <option value="today">Today</option>
                                    <option value="all_time" selected>All Time</option>
                                </select>
                                <div class="pointer-events-none absolute right-0 flex items-center text-slate-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Metric -->
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 hover:text-slate-700 cursor-help transition border-b border-dashed border-slate-300 w-max" title="Number of orders placed in selected timeframe.">Orders</span>
                        <span id="metric-orders" class="text-xl font-black text-slate-900 mt-1 block">{{ $allTimeOrdersCount }}</span>
                    </div>
                    <!-- Sparkline -->
                    <div class="text-blue-500 opacity-60">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0,15 Q12,5 25,10 T50,2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>

                <!-- Items Ordered Metric -->
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 hover:text-slate-700 cursor-help transition border-b border-dashed border-slate-300 w-max" title="Total item quantities ordered across all matching purchases.">Items ordered</span>
                        <span id="metric-items" class="text-xl font-black text-slate-900 mt-1 block">{{ $allTimeItemsCount }}</span>
                    </div>
                    <!-- Sparkline -->
                    <div class="text-emerald-500 opacity-60">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0,18 L10,12 L20,15 L30,5 L40,8 L50,2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>

                <!-- Returns Metric -->
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 hover:text-slate-700 cursor-help transition border-b border-dashed border-slate-300 w-max" title="Refunds and returns amount in local currency.">Returns</span>
                        <span id="metric-returns" class="text-xl font-black text-slate-900 mt-1 block">PKR {{ number_format($allTimeReturnsAmount) }}</span>
                    </div>
                    <!-- Sparkline -->
                    <div class="text-slate-300 opacity-70">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <line x1="0" y1="10" x2="50" y2="10" stroke-dasharray="3,3"></line>
                        </svg>
                    </div>
                </div>

                <!-- Orders Fulfilled Metric -->
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 hover:text-slate-700 cursor-help transition border-b border-dashed border-slate-300 w-max" title="Orders that have been moved to processing or completed status.">Orders fulfilled</span>
                        <span id="metric-fulfilled" class="text-xl font-black text-slate-900 mt-1 block">{{ $allTimeFulfilledCount }}</span>
                    </div>
                    <!-- Sparkline -->
                    <div class="text-blue-500 opacity-60">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0,16 Q15,4 30,12 T50,4" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>

                <!-- Orders Delivered Metric -->
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 hover:text-slate-700 cursor-help transition border-b border-dashed border-slate-300 w-max" title="Orders that are completed and successfully delivered.">Orders delivered</span>
                        <span id="metric-delivered" class="text-xl font-black text-slate-900 mt-1 block">{{ $allTimeDeliveredCount }}</span>
                    </div>
                    <!-- Sparkline -->
                    <div class="text-emerald-500 opacity-60">
                        <svg class="w-12 h-6" viewBox="0 0 50 20" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M0,15 L12,10 L25,12 L38,4 L50,2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 space-y-6 text-left">
            <div class="absolute top-0 left-0 w-32 h-[4px] bg-emerald-500"></div>
            
            <!-- Orders Header Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Orders Registry 🛍️</h1>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Track storefront purchases, check delivery locations, and update fulfillment status.</p>
                </div>
                <div class="bg-indigo-50 border border-indigo-150/40 text-indigo-700 font-extrabold px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 shrink-0 shadow-sm">
                    <span>Total Orders:</span> <span class="bg-indigo-600 text-white px-2 py-0.5 rounded-lg font-black">{{ $orders->count() }}</span>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div id="bulk-actions-bar" class="hidden bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between transition-all duration-300">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-indigo-900">
                        <span id="selected-count" class="bg-indigo-600 text-white px-2.5 py-0.5 rounded-lg font-black mr-1 shadow-sm">0</span> orders selected
                    </span>
                </div>
                <form action="/shop/orders/bulk-delete" method="POST" id="bulk-delete-form" onsubmit="return confirm('Are you sure you want to permanently delete the selected orders? This action cannot be undone.')">
                    @csrf
                    <input type="hidden" name="order_ids" id="bulk-order-ids">
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-4.5 py-2.5 rounded-xl transition duration-150 shadow-sm text-xs flex items-center gap-1.5">
                        🗑️ <span>Delete Selected Orders</span>
                    </button>
                </form>
            </div>

            <!-- Orders List Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-150 bg-white">
                <table class="min-w-full leading-normal text-left text-xs font-medium text-slate-600">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-150 text-slate-500 font-extrabold uppercase tracking-wider">
                            <th class="px-4 py-4 w-12 text-center">
                                <input type="checkbox" id="select-all-checkboxes" class="rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="px-4 py-4">Order</th>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Customer</th>
                            <th class="px-4 py-4">Channel</th>
                            <th class="px-4 py-4">Total</th>
                            <th class="px-4 py-4">Payment status</th>
                            <th class="px-4 py-4">Fulfillment status</th>
                            <th class="px-4 py-4">Items</th>
                            <th class="px-4 py-4">Delivery status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold">
                        @forelse($orders as $order)
                            <tr onclick="window.location.href='/shop/orders/{{ $order->id }}'" class="hover:bg-slate-50/50 cursor-pointer transition duration-150">
                                <td class="px-4 py-4 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" value="{{ $order->id }}" class="order-checkbox rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-extrabold text-slate-900 hover:text-indigo-600 transition">#{{ $order->id }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-500 font-semibold" style="font-family: sans-serif;">
                                    {{ $order->created_at->format('l \a\t g:i a') }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-black text-slate-900 hover:text-indigo-600 transition">{{ $order->customer_name }}</span>
                                </td>
                                <td class="px-4 py-4 text-slate-400 font-bold">
                                    Online Store
                                </td>
                                <td class="px-4 py-4 font-black text-slate-900" style="font-family: sans-serif;">
                                    Rs {{ number_format($order->total) }}.00
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $payBg = 'bg-amber-100 text-amber-800 border-amber-200';
                                        $payText = 'Payment pending';
                                        if ($order->status === 'completed') {
                                            $payBg = 'bg-emerald-100/80 text-emerald-800 border-emerald-200';
                                            $payText = 'Paid';
                                        } elseif ($order->status === 'cancelled') {
                                            $payBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                            $payText = 'Voided';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-bold rounded-full border {{ $payBg }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ $payText }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $fulBg = 'bg-amber-100 text-amber-800 border-amber-200';
                                        $fulText = 'Unfulfilled';
                                        if ($order->status === 'completed') {
                                            $fulBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                            $fulText = 'Fulfilled';
                                        } elseif ($order->status === 'processing') {
                                            $fulBg = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                            $fulText = 'On hold';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-bold rounded-full border {{ $fulBg }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        {{ $fulText }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-500 font-bold" style="font-family: sans-serif;">
                                    @php
                                        $qtySum = is_array($order->cart_items) ? collect($order->cart_items)->sum('qty') : 0;
                                    @endphp
                                    {{ $qtySum }} {{ Str::plural('item', $qtySum) }}
                                </td>
                                <td class="px-4 py-4">
                                    @if($order->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-200">
                                            Delivered
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-extrabold text-rose-700 bg-rose-50 rounded-lg border border-rose-200">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-extrabold text-slate-600 bg-slate-50 rounded-lg border border-slate-200">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-slate-400 font-bold border-2 border-dashed border-slate-150 bg-slate-50/50 rounded-2xl">
                                    <span class="text-4xl block mb-3">🛒</span>
                                    No storefront orders have been received yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Interactive Timeframe Switching Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeframeSelect = document.getElementById('timeframe-select');
            
            // Hydrated statistics from backend
            const stats = {
                today: {
                    orders: "{{ $todayOrdersCount }}",
                    items: "{{ $todayItemsCount }}",
                    returns: "PKR {{ number_format($todayReturnsAmount) }}",
                    fulfilled: "{{ $todayFulfilledCount }}",
                    delivered: "{{ $todayDeliveredCount }}"
                },
                all_time: {
                    orders: "{{ $allTimeOrdersCount }}",
                    items: "{{ $allTimeItemsCount }}",
                    returns: "PKR {{ number_format($allTimeReturnsAmount) }}",
                    fulfilled: "{{ $allTimeFulfilledCount }}",
                    delivered: "{{ $allTimeDeliveredCount }}"
                }
            };

            function updateMetrics(timeframe) {
                const metric = stats[timeframe];
                if (!metric) return;
                
                document.getElementById('metric-orders').textContent = metric.orders;
                document.getElementById('metric-items').textContent = metric.items;
                document.getElementById('metric-returns').textContent = metric.returns;
                document.getElementById('metric-fulfilled').textContent = metric.fulfilled;
                document.getElementById('metric-delivered').textContent = metric.delivered;
            }

            timeframeSelect.addEventListener('change', function(e) {
                updateMetrics(e.target.value);
            });

            // Initialize default view
            updateMetrics('all_time');

            // --- Bulk Actions Javascript ---
            const selectAllCheckbox = document.getElementById('select-all-checkboxes');
            const rowCheckboxes = document.querySelectorAll('.order-checkbox');
            const bulkActionsBar = document.getElementById('bulk-actions-bar');
            const selectedCountSpan = document.getElementById('selected-count');
            const bulkOrderIdsInput = document.getElementById('bulk-order-ids');

            function updateBulkActionsBar() {
                const checkedIds = Array.from(rowCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                if (checkedIds.length > 0) {
                    selectedCountSpan.textContent = checkedIds.length;
                    bulkOrderIdsInput.value = JSON.stringify(checkedIds);
                    bulkActionsBar.classList.remove('hidden');
                } else {
                    bulkActionsBar.classList.add('hidden');
                }
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    rowCheckboxes.forEach(cb => {
                        cb.checked = selectAllCheckbox.checked;
                    });
                    updateBulkActionsBar();
                });
            }

            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                    const noneChecked = Array.from(rowCheckboxes).every(c => !c.checked);
                    
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = !allChecked && !noneChecked;
                    }
                    updateBulkActionsBar();
                });
            });
        });
    </script>
</body>
</html>
