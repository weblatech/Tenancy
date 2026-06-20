@php
    if (!function_exists('getInitials')) {
        function getInitials($name) {
            $words = explode(' ', trim($name));
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
            return $initials ?: 'C';
        }
    }
    if (!function_exists('getAvatarColorClass')) {
        function getAvatarColorClass($name) {
            $colors = [
                'bg-rose-500', 'bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 
                'bg-violet-500', 'bg-sky-500', 'bg-fuchsia-500', 'bg-teal-500'
            ];
            $hash = crc32($name);
            return $colors[abs($hash) % count($colors)];
        }
    }
    $initials = getInitials($customer->name);
    $avatarColor = getAvatarColorClass($customer->name);

    // Format WhatsApp link
    $whatsappLink = '';
    if ($customer->phone) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '92' . substr($cleanPhone, 1);
        }
        $whatsappLink = "https://wa.me/" . $cleanPhone;
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer: {{ $customer->name }} — Details</title>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Customer Profile</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop/customers" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Back to Directory</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="relative z-10 max-w-7xl mx-auto mt-10 px-6">
        
        <!-- Breadcrumbs -->
        <div class="mb-6">
            <a href="/shop/customers" class="inline-flex items-center gap-1 text-xs font-black text-indigo-600 hover:text-indigo-700 transition">
                <span>← Back to Customers</span>
            </a>
        </div>

        <!-- Two-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Details & Statistics -->
            <div class="space-y-6 lg:col-span-1">
                
                <!-- Profile Card -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex flex-col items-center text-center">
                    <div class="absolute top-0 left-0 w-20 h-[3px] bg-indigo-500"></div>
                    <!-- Avatar circle -->
                    <div class="w-20 h-20 rounded-full text-white {{ $avatarColor }} flex items-center justify-center font-black text-3xl shadow-md mb-4">
                        {{ $initials }}
                    </div>
                    
                    <h2 class="text-xl font-black text-slate-900 leading-tight">{{ $customer->name }}</h2>
                    @if(isset($customer->is_guest) && $customer->is_guest)
                        <span class="bg-slate-100 text-slate-500 border border-slate-200 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider mt-2">Guest Customer</span>
                    @else
                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 text-[9px] px-2.5 py-0.5 rounded-full font-black uppercase tracking-wider mt-2">Registered Account</span>
                    @endif

                    <div class="w-full border-t border-slate-100 mt-6 pt-5 space-y-4 text-left">
                        <!-- Email row -->
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Email Address</span>
                            <span class="text-xs font-black text-slate-700 block mt-0.5 select-all">{{ $customer->email }}</span>
                        </div>
                        
                        <!-- Phone row -->
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Phone Number</span>
                            @if($customer->phone)
                                <span class="text-xs font-black text-slate-700 block mt-0.5 select-all" style="font-family: sans-serif;">{{ $customer->phone }}</span>
                            @else
                                <span class="text-xs font-semibold text-slate-400 block mt-0.5">—</span>
                            @endif
                        </div>

                        <!-- Location City row -->
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">City</span>
                            <span class="text-xs font-black text-slate-700 block mt-0.5">{{ $customer->city ?: 'Not Specified' }}</span>
                        </div>

                        <!-- Full Address row -->
                        <div>
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Full Address</span>
                            <span class="text-xs font-bold text-slate-600 block mt-0.5 leading-relaxed">{{ $customer->address ?: 'Not Specified' }}</span>
                        </div>

                        <!-- Registration Date row -->
                        @if($customer->created_at)
                            <div>
                                <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Member Since</span>
                                <span class="text-xs font-bold text-slate-500 block mt-0.5" style="font-family: sans-serif;">{{ \Carbon\Carbon::parse($customer->created_at)->format('d F Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Direct Actions (WhatsApp & Mail) -->
                    <div class="w-full grid grid-cols-2 gap-3 mt-6 pt-5 border-t border-slate-100">
                        @if($whatsappLink)
                            <a href="{{ $whatsappLink }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs px-4 py-3 rounded-xl flex items-center justify-center gap-1.5 transition shadow-md shadow-emerald-600/10">
                                💬 <span>WhatsApp</span>
                            </a>
                        @else
                            <button disabled class="bg-slate-100 text-slate-300 font-extrabold text-xs px-4 py-3 rounded-xl flex items-center justify-center gap-1.5 cursor-not-allowed">
                                💬 <span>WhatsApp</span>
                            </button>
                        @endif

                        @if($customer->email && $customer->email !== 'Guest Customer')
                            <a href="mailto:{{ $customer->email }}?subject=Message%20from%20{{ urlencode(tenant('name') ?? 'Store') }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs px-4 py-3 rounded-xl flex items-center justify-center gap-1.5 transition">
                                ✉️ <span>Send Email</span>
                            </a>
                        @else
                            <button disabled class="bg-slate-100 text-slate-300 font-extrabold text-xs px-4 py-3 rounded-xl flex items-center justify-center gap-1.5 cursor-not-allowed">
                                ✉️ <span>Send Email</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Financial Statistics Card -->
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm space-y-5">
                    <div class="absolute top-0 left-0 w-20 h-[3px] bg-indigo-500"></div>
                    <h3 class="text-base font-black text-slate-900 border-b border-slate-100 pb-3">Lifetime Value</h3>
                    
                    <div class="grid grid-cols-3 divide-x divide-slate-100">
                        <!-- Total spent -->
                        <div class="px-2">
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Total Spent</span>
                            <span class="text-lg font-black text-emerald-600 mt-1 block" style="font-family: sans-serif;">Rs {{ number_format($totalSpent) }}</span>
                        </div>
                        
                        <!-- Orders count -->
                        <div class="px-4">
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Orders</span>
                            <span class="text-lg font-black text-slate-900 mt-1 block" style="font-family: sans-serif;">{{ $orders->count() }}</span>
                        </div>

                        <!-- Average order value -->
                        <div class="px-4">
                            <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">Avg Order</span>
                            <span class="text-lg font-black text-slate-700 mt-1 block" style="font-family: sans-serif;">Rs {{ number_format($aov) }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Order History -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 md:p-8 space-y-6">
                    <div class="absolute top-0 left-0 w-32 h-[4px] bg-indigo-500"></div>
                    <div class="border-b border-slate-100 pb-5">
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">Order History 🛍️</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Registry of storefront orders placed by this customer.</p>
                    </div>

                    <!-- Orders table -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-150 bg-white">
                        <table class="min-w-full leading-normal text-left text-xs font-medium text-slate-600">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-150 text-slate-500 font-extrabold uppercase tracking-wider">
                                    <th class="px-4 py-4">Order ID</th>
                                    <th class="px-4 py-4">Date</th>
                                    <th class="px-4 py-4">Items</th>
                                    <th class="px-4 py-4">Payment Method</th>
                                    <th class="px-4 py-4">Fulfillment</th>
                                    <th class="px-4 py-4">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold">
                                @forelse($orders as $order)
                                    <tr onclick="window.location.href='/shop/orders/{{ $order->id }}'" class="hover:bg-slate-50/50 cursor-pointer transition duration-150">
                                        <td class="px-4 py-4">
                                            <span class="font-extrabold text-indigo-600 hover:text-indigo-700">#{{ $order->id }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-slate-500 font-medium" style="font-family: sans-serif;">
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-4 py-4 text-slate-500 font-bold" style="font-family: sans-serif;">
                                            @php
                                                $qtySum = is_array($order->cart_items) ? collect($order->cart_items)->sum('qty') : 0;
                                            @endphp
                                            {{ $qtySum }} {{ Str::plural('item', $qtySum) }}
                                        </td>
                                        <td class="px-4 py-4 text-slate-400 font-bold uppercase">
                                            {{ $order->payment_method ?? 'COD' }}
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
                                                } elseif ($order->status === 'cancelled') {
                                                    $fulBg = 'bg-rose-100 text-rose-800 border-rose-200';
                                                    $fulText = 'Cancelled';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold rounded-full border {{ $fulBg }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                {{ $fulText }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 font-black text-slate-900" style="font-family: sans-serif;">
                                            Rs {{ number_format($order->total) }}.00
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center text-slate-400 font-bold border-2 border-dashed border-slate-150 bg-slate-50/50 rounded-2xl">
                                            <span class="text-4xl block mb-3">🛒</span>
                                            This customer has not placed any orders yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>
