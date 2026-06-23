<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->enable_rtl ? 'میرا ڈیش بورڈ' : 'My Dashboard' }} - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if($settings->enable_rtl)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pakeezah-fonts/jameel-noori-nastaleeq@1.0.0/index.css">
    <style>
        html { font-size: 115% !important; }
        body { direction: rtl; text-align: right; font-family: 'Jameel Noori Nastaleeq', sans-serif !important; }
        p, span, h1, h2, h3, h4, h5, h6, a, button, input { line-height: 1.6 !important; }
    </style>
    @endif

    <style>
        .btn-primary-custom {
            background-color: {{ $settings->btn_primary_bg ?? '#16a34a' }} !important;
            color: {{ $settings->btn_primary_text ?? '#ffffff' }} !important;
        }
        .btn-primary-custom:hover { opacity: 0.95; }
        
        .btn-secondary-custom {
            background-color: {{ $settings->btn_secondary_bg ?? '#1f2937' }} !important;
            color: {{ $settings->btn_secondary_text ?? '#ffffff' }} !important;
        }
        .btn-secondary-custom:hover { opacity: 0.95; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col font-sans">

    <!-- Header / Nav -->
    <header class="bg-white border-b border-slate-100 py-5 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ tenant_store_url('/') }}" class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-1.5">
                🛍️ {{ strtoupper($tenantId) }}
            </a>
            
            <div class="flex items-center gap-4">
                <a href="/collection" class="text-xs font-bold text-indigo-600 hover:underline">
                    {{ $settings->enable_rtl ? 'خریداری جاری رکھیں' : 'Continue Shopping' }}
                </a>
                <form action="/customer/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-500 hover:underline">
                        {{ $settings->enable_rtl ? 'لاگ آؤٹ' : 'Log Out' }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">
        
        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-250 text-emerald-700 text-xs font-bold rounded-2xl flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Profile Information (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-150 p-6 md:p-8 shadow-xl space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-800 border-b border-slate-100 pb-3">
                        {{ $settings->enable_rtl ? 'میری معلومات' : 'My Profile' }}
                    </h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="block text-[9px] uppercase font-black text-slate-400">{{ $settings->enable_rtl ? 'نام' : 'Full Name' }}</span>
                        <span class="text-xs font-bold text-slate-700 block mt-1">{{ $customer->name }}</span>
                    </div>

                    <div>
                        <span class="block text-[9px] uppercase font-black text-slate-400">{{ $settings->enable_rtl ? 'ای میل' : 'Email Address' }}</span>
                        <span class="text-xs font-bold text-slate-700 block mt-1">{{ $customer->email }}</span>
                    </div>

                    <div>
                        <span class="block text-[9px] uppercase font-black text-slate-400">{{ $settings->enable_rtl ? 'فون نمبر' : 'Phone' }}</span>
                        <span class="text-xs font-bold text-slate-700 block mt-1">{{ $customer->phone ?? ($settings->enable_rtl ? 'دستیاب نہیں' : 'Not Provided') }}</span>
                    </div>

                    <div>
                        <span class="block text-[9px] uppercase font-black text-slate-400">{{ $settings->enable_rtl ? 'شہر' : 'City' }}</span>
                        <span class="text-xs font-bold text-slate-700 block mt-1">{{ $customer->city ?? ($settings->enable_rtl ? 'دستیاب نہیں' : 'Not Provided') }}</span>
                    </div>

                    <div>
                        <span class="block text-[9px] uppercase font-black text-slate-400">{{ $settings->enable_rtl ? 'ایڈریس' : 'Address' }}</span>
                        <span class="text-xs font-bold text-slate-700 block mt-1 leading-relaxed">{{ $customer->address ?? ($settings->enable_rtl ? 'دستیاب نہیں' : 'Not Provided') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order History (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-150 p-6 md:p-8 shadow-xl space-y-6">
                <div>
                    <h3 class="text-lg font-black text-slate-800 border-b border-slate-100 pb-3">
                        {{ $settings->enable_rtl ? 'میرا آرڈر ریکارڈ' : 'Order History' }}
                    </h3>
                </div>

                @if($orders->isEmpty())
                    <div class="text-center py-12 space-y-4">
                        <div class="text-4xl">📦</div>
                        <h4 class="text-sm font-black text-slate-600">{{ $settings->enable_rtl ? 'آپ کا کوئی آرڈر ریکارڈ نہیں ہے' : 'You have not placed any orders yet.' }}</h4>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">{{ $settings->enable_rtl ? 'ہمارے پاس بہترین پروڈکٹس دستیاب ہیں، ابھی خریداری شروع کریں۔' : 'Browse our collection and order outstanding health and wellness products today!' }}</p>
                        <a href="/collection" class="btn-primary-custom inline-flex items-center gap-1.5 text-xs font-black px-5 py-3 rounded-xl transition shadow-md">
                            {{ $settings->enable_rtl ? 'خریداری کریں' : 'Start Shopping' }}
                        </a>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($orders as $order)
                            <div class="border border-slate-150 rounded-2xl overflow-hidden shadow-sm">
                                <!-- Order Header -->
                                <div class="bg-slate-50 px-5 py-4 border-b border-slate-150 flex flex-wrap justify-between items-center gap-2">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-black text-slate-800">
                                            {{ $settings->enable_rtl ? 'آرڈر #' : 'Order #' }}{{ $order->id }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-semibold">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        @php
                                            $statusClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            $statusText = $order->status;
                                            if ($order->status === 'completed') {
                                                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-250';
                                                $statusText = $settings->enable_rtl ? 'مکمل' : 'Completed';
                                            } elseif ($order->status === 'processing') {
                                                $statusClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                                $statusText = $settings->enable_rtl ? 'پروسیسنگ' : 'Processing';
                                            } elseif ($order->status === 'cancelled') {
                                                $statusClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $statusText = $settings->enable_rtl ? 'منسوخ' : 'Cancelled';
                                            } else {
                                                $statusText = $settings->enable_rtl ? 'زیر التواء' : 'Pending';
                                            }
                                        @endphp
                                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Body -->
                                <div class="p-5 space-y-4">
                                    <!-- Items -->
                                    <div class="divide-y divide-slate-100">
                                        @if(is_array($order->cart_items))
                                            @foreach($order->cart_items as $item)
                                                <div class="py-2.5 flex items-center justify-between text-xs font-semibold text-slate-700 first:pt-0 last:pb-0">
                                                    <div>
                                                        <span class="font-extrabold text-slate-800">{{ $item['title'] ?? 'Product' }}</span>
                                                        @if(!empty($item['variant']))
                                                            <span class="text-[9px] text-slate-400 block mt-0.5">{{ $item['variant'] }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-slate-400 font-bold">
                                                        {{ $item['qty'] ?? 1 }} × {{ $settings->enable_rtl ? 'روپے' : 'Rs.' }} {{ number_format($item['price'] ?? 0) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <!-- Price Breakdown -->
                                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-xs font-bold text-slate-500">
                                        <div>
                                            {{ $settings->enable_rtl ? 'ٹوٹل قیمت' : 'Total Amount' }}
                                        </div>
                                        <div class="text-sm font-black text-slate-800">
                                            {{ $settings->enable_rtl ? 'روپے' : 'Rs.' }} {{ number_format($order->total) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs font-bold text-slate-400">
        &copy; {{ date('Y') }} {{ strtoupper($tenantId) }}. All rights reserved.
    </footer>

</body>
</html>
