<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Customers Directory</title>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Customers Hub</span>
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
        
        <!-- Page Title Header & Search -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Customers</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">View and manage your customer base</p>
            </div>
            
            <!-- Search Bar inside Page Header -->
            <div class="relative w-full md:w-80 shadow-sm rounded-2xl">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" id="customer-search-input" placeholder="Search customers..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-2xl text-xs font-bold outline-none text-slate-700 placeholder-slate-400 transition">
            </div>
        </div>

        <!-- KPI Metrics Ribbon (Row of Cards matching mockup) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Card 1: Total Customers -->
            <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex items-center gap-5">
                <div class="absolute top-0 left-0 w-16 h-[3px] bg-indigo-500"></div>
                <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-3xl font-black text-slate-900 block" style="font-family: sans-serif;">{{ $totalCustomers }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Customers</span>
                </div>
            </div>

            <!-- Card 2: New This Month -->
            <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex items-center gap-5">
                <div class="absolute top-0 left-0 w-16 h-[3px] bg-indigo-500"></div>
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-3xl font-black text-slate-900 block" style="font-family: sans-serif;">{{ $newThisMonth }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">New This Month</span>
                </div>
            </div>

            <!-- Card 3: Repeat Customers -->
            <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex items-center gap-5">
                <div class="absolute top-0 left-0 w-16 h-[3px] bg-indigo-500"></div>
                <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12"/>
                    </svg>
                </div>
                <div>
                    <span class="text-3xl font-black text-slate-900 block" style="font-family: sans-serif;">{{ $repeatCustomers }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Repeat Customers</span>
                </div>
            </div>

            <!-- Card 4: Cities Served -->
            <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex items-center gap-5">
                <div class="absolute top-0 left-0 w-16 h-[3px] bg-indigo-500"></div>
                <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-3xl font-black text-slate-900 block" style="font-family: sans-serif;">{{ $citiesServed }}</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cities Served</span>
                </div>
            </div>

        </div>

        <!-- Main Customer Directory Panel -->
        <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 space-y-6 text-left">
            <div class="absolute top-0 left-0 w-32 h-[4px] bg-indigo-500"></div>
            
            <!-- Registry Header Controls -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="bg-indigo-50 text-indigo-600 p-2 rounded-xl text-lg">
                        👥
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Customer Directory</h2>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">Detailed log of registered buyers and guest checkouts.</p>
                    </div>
                </div>
                <!-- Export CSV Link -->
                <a href="/shop/customers/export" id="export-customers-btn" class="bg-slate-50 border border-slate-200 text-slate-700 font-extrabold px-4 py-2.5 rounded-xl text-xs hover:bg-slate-100 hover:text-slate-950 transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export</span>
                </a>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center bg-slate-50/80 border border-slate-200 p-5 rounded-3xl backdrop-blur-sm shadow-inner">
                <div class="flex flex-wrap gap-4 items-center">
                    
                    <!-- Group Filter -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-slate-400 flex items-center gap-1 uppercase tracking-wider">
                            👤 <span>Group:</span>
                        </span>
                        <select id="filter-type" onchange="applyFilters()" class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition cursor-pointer shadow-sm hover:border-slate-300">
                            <option value="all">Total Customers (ٹوٹل کسٹمرز)</option>
                            <option value="new">New Customers / 1 Order (نئے کسٹمرز / 1 آرڈر)</option>
                            <option value="this_month">This Month Customers (اس ماہ کے کسٹمرز)</option>
                            <option value="repeat">Repeat Customers (بار بار آنے والے)</option>
                        </select>
                    </div>
 
                    <!-- Delivery Status Filter -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-slate-400 flex items-center gap-1 uppercase tracking-wider">
                            🚚 <span>Delivery:</span>
                        </span>
                        <select id="filter-delivery" onchange="applyFilters()" class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-4 py-2.5 rounded-xl focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition cursor-pointer shadow-sm hover:border-slate-300">
                            <option value="all">All Deliveries (تمام آرڈرز)</option>
                            <option value="delivered">Received Orders (آرڈر موصول ہو گیا)</option>
                            <option value="returned">Returned / Cancelled (واپس / کینسل ہوا)</option>
                            <option value="no_orders">No Orders (کوئی آرڈر نہیں دیا)</option>
                        </select>
                    </div>

                    @if(request('type', 'all') !== 'all' || request('delivery', 'all') !== 'all' || request('search') !== '')
                        <a href="/shop/customers" class="text-xs font-black text-rose-600 hover:text-rose-700 hover:underline transition flex items-center gap-1.5 bg-rose-50 border border-rose-200 px-3 py-2 rounded-xl">
                            ✕ <span>Reset (فلٹر ختم کریں)</span>
                        </a>
                    @endif
 
                </div>
 
                <!-- Showing indicator -->
                <div class="text-xs font-bold text-slate-400 self-end lg:self-center bg-slate-100/80 px-3.5 py-1.5 rounded-full border border-slate-200 shadow-sm">
                    Showing <span class="text-slate-800 font-extrabold">{{ $customers->count() }}</span> of <span class="text-slate-800 font-extrabold">{{ $totalCustomers }}</span> customers
                </div>
            </div>

            <!-- Customer Directory Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-150 bg-white">
                <table class="min-w-full leading-normal text-left text-xs font-medium text-slate-600">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-150 text-slate-500 font-extrabold uppercase tracking-wider">
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Mobile</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4 text-center">Orders</th>
                            <th class="px-6 py-4">Total Spent</th>
                            <th class="px-6 py-4">Last Order</th>
                        </tr>
                    </thead>
                    <tbody id="customers-table-body" class="divide-y divide-slate-100 font-semibold">
                        @forelse($customers as $customer)
                            @php
                                $detailUrl = $customer->is_guest 
                                    ? "/shop/customers/guest/{$customer->guest_key}" 
                                    : "/shop/customers/{$customer->id}";
                            @endphp
                            <tr onclick="window.location.href='{{ $detailUrl }}'" class="customer-row hover:bg-slate-50/50 cursor-pointer transition duration-150">
                                <!-- CUSTOMER: Initials Avatar + Name & Email -->
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full text-white {{ $customer->avatar_color }} flex items-center justify-center font-extrabold text-xs shadow-inner">
                                        {{ $customer->initials }}
                                    </div>
                                    <div>
                                        <span class="font-black text-slate-900 hover:text-indigo-600 transition block text-sm">{{ $customer->name }}</span>
                                        @if($customer->is_guest)
                                            <span class="bg-slate-100 text-slate-500 text-[9px] px-2 py-0.5 rounded border font-black uppercase tracking-wide inline-block mt-0.5">Guest Buyer</span>
                                        @else
                                            <span class="text-[10px] text-slate-400 font-medium tracking-normal block mt-0.5">{{ $customer->email }}</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- MOBILE -->
                                <td class="px-6 py-4 text-slate-500 font-bold">
                                    @if($customer->phone)
                                        <span class="flex items-center gap-1.5 font-medium" style="font-family: sans-serif;">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.221.908l-1.06 1.06a11.224 11.224 0 005.478 5.478l1.06-1.06a1 1 0 01.908-.221l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $customer->phone }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>

                                <!-- LOCATION -->
                                <td class="px-6 py-4">
                                    <span class="flex items-center gap-1.5 text-slate-600 font-semibold line-clamp-1 max-w-xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $customer->city ?: 'Unknown' }}{{ $customer->address ? ', ' . Str::limit($customer->address, 30) : '' }}
                                    </span>
                                </td>

                                <!-- ORDERS -->
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block bg-indigo-50 text-indigo-600 font-extrabold px-3 py-1 rounded-full text-[11px]" style="font-family: sans-serif;">
                                        {{ $customer->orders_count }}
                                    </span>
                                </td>

                                <!-- TOTAL SPENT -->
                                <td class="px-6 py-4 text-emerald-600 font-black text-sm" style="font-family: sans-serif;">
                                    Rs {{ number_format($customer->total_spent) }}
                                </td>

                                <!-- LAST ORDER -->
                                <td class="px-6 py-4 text-slate-500 font-bold" style="font-family: sans-serif;">
                                    {{ $customer->last_order ? $customer->last_order->format('d M Y') : 'No orders' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-400 font-bold border-2 border-dashed border-slate-150 bg-slate-50/50 rounded-2xl">
                                    <span class="text-4xl block mb-3">👥</span>
                                    No customers found matching active filters. (کوئی کسٹمرز نہیں ملے)
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Client-side Instant Search & Server-side Filter Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial select values from query parameters on load
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type') || 'all';
            const delivery = urlParams.get('delivery') || 'all';
            const search = urlParams.get('search') || '';

            document.getElementById('filter-type').value = type;
            document.getElementById('filter-delivery').value = delivery;
            document.getElementById('customer-search-input').value = search;

            updateExportLink();

            // Client-side search and dynamic link update on typing
            const searchInput = document.getElementById('customer-search-input');
            const tableRows = document.querySelectorAll('.customer-row');

            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    const content = row.innerText.toLowerCase();
                    if (content.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateExportLink();
            });

            // Also reload the page on search submit (Enter key) to pull fresh data
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });
        });

        function updateExportLink() {
            const type = document.getElementById('filter-type').value;
            const delivery = document.getElementById('filter-delivery').value;
            const search = document.getElementById('customer-search-input').value;
            
            const exportBtn = document.getElementById('export-customers-btn');
            if (exportBtn) {
                exportBtn.href = `/shop/customers/export?type=${type}&delivery=${delivery}&search=${encodeURIComponent(search)}`;
            }
        }

        function applyFilters() {
            const type = document.getElementById('filter-type').value;
            const delivery = document.getElementById('filter-delivery').value;
            const search = document.getElementById('customer-search-input').value;

            // Redirect with query parameters
            window.location.href = `/shop/customers?type=${type}&delivery=${delivery}&search=${encodeURIComponent(search)}`;
        }
    </script>
</body>
</html>
