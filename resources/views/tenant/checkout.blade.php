<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->enable_rtl ? 'چیک آؤٹ' : 'Checkout' }} - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if(!empty($settings->facebook_pixel_id))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings->facebook_pixel_id }}');
        fbq('track', 'PageView');
    </script>
    @endif

    @if($settings->enable_rtl)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pakeezah-fonts/jameel-noori-nastaleeq@1.0.0/index.css">
    <style>
        html {
            font-size: 115% !important; /* Scale up all rem-based font sizes for Urdu Nastaleeq readability */
        }
        body {
            direction: rtl;
            text-align: right;
            font-family: 'Jameel Noori Nastaleeq', sans-serif !important;
        }
        p, span, h1, h2, h3, h4, h5, h6, a, button, input, select, textarea {
            line-height: 1.6 !important; /* Taller line-height to prevent Nastaleeq descender/ascender overlap */
        }
    </style>
    @endif

    <style>
        /* Dynamic Stylesheet */
        .store-logo-img {
            height: {{ $settings->header_logo_height ?? 56 }}px !important;
        }
        .btn-primary-custom {
            background-color: {{ $settings->btn_primary_bg ?? '#16a34a' }} !important;
            color: {{ $settings->btn_primary_text ?? '#ffffff' }} !important;
        }
        .btn-primary-custom:hover {
            opacity: 0.9 !important;
        }
        .btn-secondary-custom {
            background-color: {{ $settings->btn_secondary_bg ?? '#1f2937' }} !important;
            color: {{ $settings->btn_secondary_text ?? '#ffffff' }} !important;
        }
        .btn-secondary-custom:hover {
            opacity: 0.9 !important;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans flex flex-col min-h-screen">

    <!-- Header / Branding -->
    <header class="bg-white border-b border-gray-100 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="/">
                @if($settings && $settings->header_logo)
                    <img class="w-auto object-contain store-logo-img" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">
                @else
                    <span class="text-2xl font-black text-gray-900 tracking-tight">🛍️ {{ strtoupper($tenantId) }}</span>
                @endif
            </a>
            <a href="/collection" class="text-sm font-bold text-green-600 hover:underline flex items-center gap-1">
                {{ $settings->enable_rtl ? '← شاپ پر واپس جائیں' : '← Return to Shop' }}
            </a>
        </div>
    </header>

    <!-- Checkout Main Content -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">
        
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl font-bold mb-8 text-center">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left Side: Shipping / Billing Form (7 cols) -->
            <div class="lg:col-span-7 space-y-8">
                <div class="bg-white rounded-3xl border border-gray-150/80 shadow-xl p-8 md:p-10">
                    <h2 class="text-2xl font-black text-gray-900 mb-6 pb-4 border-b border-gray-100">
                        {{ $settings->enable_rtl ? 'شپنگ اور ڈیلیوری کی معلومات' : 'Shipping & Delivery Information' }}
                    </h2>

                    <form action="/checkout" method="POST" id="checkoutForm" class="space-y-6">
                        @csrf
                        
                        <!-- Hidden cart payload & Geolocation tracking -->
                        <input type="hidden" name="cart_items_json" id="cart_items_json">
                        <input type="hidden" name="client_ip" id="client_ip">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="ip_country" id="ip_country">
                        <input type="hidden" name="ip_city" id="ip_city">
                        <input type="hidden" name="ip_isp" id="ip_isp">

                        <!-- Customer Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">
                                {{ $settings->enable_rtl ? 'کسٹمر کا نام' : 'Customer Name' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_name" required 
                                value="{{ old('customer_name', auth('customer')->user()->name ?? '') }}"
                                placeholder="{{ $settings->enable_rtl ? 'مثال: محمد علی' : 'e.g. Muhammad Ali' }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-green-600 transition">
                            @error('customer_name')
                                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone / Contact -->
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">
                                {{ $settings->enable_rtl ? 'فون نمبر / واٹس ایپ' : 'Phone Number / WhatsApp' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="customer_phone" required 
                                value="{{ old('customer_phone', auth('customer')->user()->phone ?? '') }}"
                                placeholder="{{ $settings->enable_rtl ? 'مثال: 03001234567' : 'e.g. 03001234567' }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-green-600 transition"
                                style="direction: ltr; text-align: left;">
                            @error('customer_phone')
                                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">
                                {{ $settings->enable_rtl ? 'شہر' : 'City' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_city" required 
                                value="{{ old('customer_city', auth('customer')->user()->city ?? '') }}"
                                placeholder="{{ $settings->enable_rtl ? 'مثال: لاہور، کراچی، راولپنڈی' : 'e.g. Lahore, Karachi, Islamabad' }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-green-600 transition">
                            @error('customer_city')
                                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Complete Shipping Address -->
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">
                                {{ $settings->enable_rtl ? 'مکمل ایڈریس' : 'Complete Shipping Address' }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="customer_address" required rows="4"
                                placeholder="{{ $settings->enable_rtl ? 'گھر کا نمبر، گلی کا نمبر، محلہ اور مشہور قریبی جگہ درج کریں' : 'House no, Street no, Sector/Area details...' }}"
                                class="w-full px-4 py-4 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white text-base font-bold outline-none focus:border-green-600 transition leading-relaxed">{{ old('customer_address', auth('customer')->user()->address ?? '') }}</textarea>
                            @error('customer_address')
                                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method Selector -->
                        @php
                            $firstChecked = '';
                            if($settings->payment_cod_active ?? true) $firstChecked = 'cod';
                            elseif($settings->payment_bank_active ?? false) $firstChecked = 'bank';
                            elseif($settings->payment_easypaisa_active ?? false) $firstChecked = 'easypaisa';
                            elseif($settings->payment_jazzcash_active ?? false) $firstChecked = 'jazzcash';
                        @endphp
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-650 mb-2">
                                {{ $settings->enable_rtl ? 'طریقہ ادائیگی (Payment Method)' : 'Payment Method' }} <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($settings->payment_cod_active ?? true)
                                <label class="payment-method-card relative border-2 border-green-600 bg-green-50/10 hover:bg-green-50/30 p-4 rounded-2xl flex items-start gap-3.5 cursor-pointer transition shadow-sm" id="card-payment-cod">
                                    <input type="radio" name="payment_method" value="cod" {{ $firstChecked === 'cod' ? 'checked' : '' }} onchange="switchPaymentMethod('cod')" class="mt-1 accent-green-600">
                                    <div class="flex-grow">
                                        <h4 class="font-black text-slate-800 text-xs">
                                            {{ $settings->enable_rtl ? 'کیش آن ڈیلیوری (COD)' : 'Cash on Delivery (COD)' }}
                                        </h4>
                                        <p class="text-[10px] text-slate-500 font-semibold mt-1 leading-normal">
                                            {{ $settings->enable_rtl ? 'آرڈر وصول کرتے وقت ادائیگی کریں' : 'Pay in cash upon delivery' }}
                                        </p>
                                    </div>
                                    <span class="text-xl shrink-0">💵</span>
                                </label>
                                @endif

                                @if($settings->payment_bank_active ?? false)
                                <label class="payment-method-card relative border border-gray-200 hover:border-green-600 p-4 rounded-2xl flex items-start gap-3.5 cursor-pointer transition shadow-sm" id="card-payment-bank">
                                    <input type="radio" name="payment_method" value="bank" {{ $firstChecked === 'bank' ? 'checked' : '' }} onchange="switchPaymentMethod('bank')" class="mt-1 accent-green-600">
                                    <div class="flex-grow">
                                        <h4 class="font-black text-slate-800 text-xs">
                                            {{ $settings->enable_rtl ? 'بینک ٹرانسفر (Bank Transfer)' : 'Bank Account Transfer' }}
                                        </h4>
                                        <p class="text-[10px] text-slate-500 font-semibold mt-1 leading-normal">
                                            {{ $settings->enable_rtl ? 'رقم براہ راست بینک اکاؤنٹ میں بھیجیں' : 'Transfer directly to our bank account' }}
                                        </p>
                                    </div>
                                    <span class="text-xl shrink-0">🏦</span>
                                </label>
                                @endif

                                @if($settings->payment_easypaisa_active ?? false)
                                <label class="payment-method-card relative border border-gray-200 hover:border-green-600 p-4 rounded-2xl flex items-start gap-3.5 cursor-pointer transition shadow-sm" id="card-payment-easypaisa">
                                    <input type="radio" name="payment_method" value="easypaisa" {{ $firstChecked === 'easypaisa' ? 'checked' : '' }} onchange="switchPaymentMethod('easypaisa')" class="mt-1 accent-green-600">
                                    <div class="flex-grow">
                                        <h4 class="font-black text-slate-800 text-xs">
                                            {{ $settings->enable_rtl ? 'ایزی پیسہ (EasyPaisa)' : 'EasyPaisa Account' }}
                                        </h4>
                                        <p class="text-[10px] text-slate-500 font-semibold mt-1 leading-normal">
                                            {{ $settings->enable_rtl ? 'ایزی پیسہ اکاؤنٹ میں رقم بھیجیں' : 'Send payment via EasyPaisa app' }}
                                        </p>
                                    </div>
                                    <span class="text-xl shrink-0">📱</span>
                                </label>
                                @endif

                                @if($settings->payment_jazzcash_active ?? false)
                                <label class="payment-method-card relative border border-gray-200 hover:border-green-600 p-4 rounded-2xl flex items-start gap-3.5 cursor-pointer transition shadow-sm" id="card-payment-jazzcash">
                                    <input type="radio" name="payment_method" value="jazzcash" {{ $firstChecked === 'jazzcash' ? 'checked' : '' }} onchange="switchPaymentMethod('jazzcash')" class="mt-1 accent-green-600">
                                    <div class="flex-grow">
                                        <h4 class="font-black text-slate-800 text-xs">
                                            {{ $settings->enable_rtl ? 'جاز کیش (JazzCash)' : 'JazzCash Account' }}
                                        </h4>
                                        <p class="text-[10px] text-slate-500 font-semibold mt-1 leading-normal">
                                            {{ $settings->enable_rtl ? 'جاز کیش اکاؤنٹ میں رقم بھیجیں' : 'Send payment via JazzCash app' }}
                                        </p>
                                    </div>
                                    <span class="text-xl shrink-0">📱</span>
                                </label>
                                @endif
                            </div>
                        </div>

                        <!-- Dynamic Account Details Panels -->
                        @if($settings->payment_bank_active)
                        <div id="details-payment-bank" class="payment-details-panel p-6 md:p-8 bg-blue-50/40 border-2 border-blue-400 rounded-3xl hidden space-y-5 shadow-md">
                            <h4 class="font-black text-blue-900 text-lg md:text-xl flex items-center gap-2 border-b border-blue-200 pb-3 leading-normal justify-center sm:justify-start">
                                🏦 {{ $settings->enable_rtl ? 'بینک ٹرانسفر کی تفصیلات' : 'Bank Transfer Details' }}
                            </h4>
                            <div class="text-sm md:text-base text-slate-800 font-extrabold space-y-3.5 leading-relaxed">
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'بینک کا نام:' : 'Bank Name:' }}</span>
                                    <span class="text-slate-950 font-black text-base md:text-lg">{{ $settings->payment_bank_name }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-950 font-black text-base md:text-lg">{{ $settings->payment_bank_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'اکاؤنٹ نمبر / IBAN:' : 'Account Number / IBAN:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-blue-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="payment_bank_num">{{ $settings->payment_bank_number }}</span>
                                        <button type="button" onclick="copyToClipboard('payment_bank_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                            <p class="text-xs text-blue-800 font-bold pt-3 border-t border-blue-200/40 leading-normal flex gap-1.5 items-center justify-center">
                                <span>📢</span>
                                <span>{{ $settings->enable_rtl ? 'براہ کرم ادائیگی کے بعد ٹرانزیکشن کا سکرین شاٹ واٹس ایپ پر شیئر کریں۔' : 'Please share the transaction screenshot on WhatsApp after payment.' }}</span>
                            </p>
                        </div>
                        @endif

                        @if($settings->payment_easypaisa_active)
                        <div id="details-payment-easypaisa" class="payment-details-panel p-6 md:p-8 bg-emerald-50/40 border-2 border-emerald-400 rounded-3xl hidden space-y-5 shadow-md">
                            <h4 class="font-black text-emerald-900 text-lg md:text-xl flex items-center gap-2 border-b border-emerald-200 pb-3 leading-normal justify-center sm:justify-start">
                                📱 {{ $settings->enable_rtl ? 'ایزی پیسہ اکاؤنٹ کی تفصیلات' : 'EasyPaisa Details' }}
                            </h4>
                            <div class="text-sm md:text-base text-slate-800 font-extrabold space-y-3.5 leading-relaxed">
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ ہولڈر کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-950 font-black text-base md:text-lg">{{ $settings->payment_easypaisa_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'ایزی پیسہ نمبر:' : 'EasyPaisa Number:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-emerald-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="payment_easypaisa_num">{{ $settings->payment_easypaisa_number }}</span>
                                        <button type="button" onclick="copyToClipboard('payment_easypaisa_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                            <p class="text-xs text-emerald-800 font-bold pt-3 border-t border-emerald-200/40 leading-normal flex gap-1.5 items-center justify-center">
                                <span>📢</span>
                                <span>{{ $settings->enable_rtl ? 'براہ کرم رقم بھیجنے کے بعد ٹرانزیکشن کا سکرین شاٹ واٹس ایپ کریں۔' : 'Please send the transaction screenshot on WhatsApp after transferring.' }}</span>
                            </p>
                        </div>
                        @endif

                        @if($settings->payment_jazzcash_active)
                        <div id="details-payment-jazzcash" class="payment-details-panel p-6 md:p-8 bg-rose-50/40 border-2 border-rose-450 rounded-3xl hidden space-y-5 shadow-md">
                            <h4 class="font-black text-rose-900 text-lg md:text-xl flex items-center gap-2 border-b border-rose-200 pb-3 leading-normal justify-center sm:justify-start">
                                📱 {{ $settings->enable_rtl ? 'جاز کیش اکاؤنٹ کی تفصیلات' : 'JazzCash Details' }}
                            </h4>
                            <div class="text-sm md:text-base text-slate-800 font-extrabold space-y-3.5 leading-relaxed">
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ ہولڈر کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-955 font-black text-base md:text-lg">{{ $settings->payment_jazzcash_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'جاز کیش نمبر:' : 'JazzCash Number:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-rose-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="payment_jazzcash_num">{{ $settings->payment_jazzcash_number }}</span>
                                        <button type="button" onclick="copyToClipboard('payment_jazzcash_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                            <p class="text-xs text-rose-800 font-bold pt-3 border-t border-rose-200/40 leading-normal flex gap-1.5 items-center justify-center">
                                <span>📢</span>
                                <span>{{ $settings->enable_rtl ? 'براہ کرم رقم بھیجنے کے بعد ٹرانزیکشن کا سکرین شاٹ واٹس ایپ پر شیئر کریں۔' : 'Please share the transaction screenshot on WhatsApp after transferring.' }}</span>
                            </p>
                        </div>
                        @endif

                        <!-- COD Advance Details Panel -->
                        @if($settings->payment_cod_active && $settings->cod_require_advance)
                        <div id="details-payment-cod" class="payment-details-panel p-6 bg-white border-2 border-amber-250 rounded-[2rem] hidden space-y-6 shadow-xl">
                            <h4 class="font-black text-slate-950 text-xl flex items-center gap-2 border-b border-gray-150 pb-3 leading-normal justify-center sm:justify-start">
                                💵 {{ $settings->enable_rtl ? 'پیشگی ادائیگی کی تفصیلات (Advance Payment Details)' : 'COD Advance Payment Details' }}
                            </h4>
                            
                            <!-- Beautiful Warning Alert -->
                            <div class="bg-red-50 border border-red-200 text-red-900 p-5 rounded-2xl text-xs md:text-sm font-bold leading-relaxed flex items-start gap-3 shadow-md">
                                <span class="text-xl shrink-0">⚠️</span>
                                <div class="space-y-1 w-full text-center sm:text-left {{ $settings->enable_rtl ? 'sm:text-right' : '' }}">
                                    <p class="font-black text-base text-red-700 leading-snug">
                                        {{ $settings->enable_rtl ? 'اہم نوٹ: جب تک ایڈوانس پیمنٹ نہیں کی جائے گی آپ کا آرڈر پروسس نہیں کیا جائے گا۔' : 'Important: Until the advance payment is received, your order will NOT be processed.' }}
                                    </p>
                                    <p class="text-xs text-red-650 font-semibold leading-normal mt-1">
                                        {{ $settings->enable_rtl ? 'Important: Until the advance payment is received, your order will NOT be processed.' : 'اہم نوٹ: جب تک ایڈوانس پیمنٹ نہیں کی جائے گی آپ کا آرڈر پروسس نہیں کیا جائے گا۔' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Required Advance Amount Card -->
                            <div class="bg-gradient-to-r from-amber-50 to-amber-50/50 border border-amber-200 p-5 rounded-2xl text-center space-y-1.5 shadow-sm">
                                <span class="text-xs text-slate-500 font-black block uppercase tracking-wider">
                                    {{ $settings->enable_rtl ? 'پیشگی ادائیگی کی رقم (Required Advance Amount)' : 'Required Advance Amount / پیشگی ادائیگی کی رقم' }}
                                </span>
                                <div class="text-3xl md:text-4xl font-black text-green-700 tracking-tight" id="cod-advance-panel-amount">
                                    Rs. 0
                                </div>
                            </div>

                            <!-- Structured Deposit Account Details -->
                            @php
                                $codPanelBg = 'bg-slate-50 border-gray-250';
                                $codPanelHeaderBg = 'bg-slate-100 text-slate-800 border-gray-200';
                                if ($settings->cod_advance_method === 'easypaisa') {
                                    $codPanelBg = 'bg-emerald-50/40 border-emerald-400';
                                    $codPanelHeaderBg = 'bg-emerald-100 text-emerald-850 border-emerald-200';
                                } elseif ($settings->cod_advance_method === 'jazzcash') {
                                    $codPanelBg = 'bg-rose-50/40 border-rose-455';
                                    $codPanelHeaderBg = 'bg-rose-100 text-rose-850 border-rose-200';
                                } elseif ($settings->cod_advance_method === 'bank') {
                                    $codPanelBg = 'bg-blue-50/40 border-blue-400';
                                    $codPanelHeaderBg = 'bg-blue-100 text-blue-850 border-blue-200';
                                }
                            @endphp

                            <div class="{{ $codPanelBg }} p-6 rounded-[2rem] border-2 space-y-5 shadow-sm mt-2.5">
                                <span class="text-sm text-slate-700 font-black block uppercase tracking-wider border-b border-gray-250 pb-2.5 flex justify-between items-center">
                                    <span>{{ $settings->enable_rtl ? 'ادائیگی کے لیے اکاؤنٹ کی معلومات:' : 'Payment Account Details:' }}</span>
                                    <span class="text-xs {{ $codPanelHeaderBg }} px-2.5 py-0.5 rounded-md border font-extrabold uppercase">Deposit Account</span>
                                </span>

                                @if($settings->cod_advance_method === 'easypaisa')
                                    <div class="space-y-4 text-sm text-slate-800">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-250/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'ادائیگی کا طریقہ:' : 'Payment Method:' }}</span>
                                            <span class="font-black text-emerald-700 bg-emerald-55 px-3 py-1 rounded-xl border border-emerald-250 text-xs">EasyPaisa 📱</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                            <span class="font-black text-slate-900 text-sm md:text-base">{{ $settings->cod_advance_easypaisa_title }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'ایزی پیسہ موبائل نمبر:' : 'EasyPaisa Mobile Number:' }}</span>
                                            <div class="flex items-center justify-between bg-white border-2 border-emerald-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                                <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="checkout_cod_easypaisa_num">{{ $settings->cod_advance_easypaisa_number }}</span>
                                                <button type="button" onclick="copyToClipboard('checkout_cod_easypaisa_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                    📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($settings->cod_advance_method === 'jazzcash')
                                    <div class="space-y-4 text-sm text-slate-800">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-250/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'ادائیگی کا طریقہ:' : 'Payment Method:' }}</span>
                                            <span class="font-black text-red-700 bg-red-55 px-3 py-1 rounded-xl border border-red-250 text-xs">JazzCash 📱</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                            <span class="font-black text-slate-900 text-sm md:text-base">{{ $settings->cod_advance_jazzcash_title }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'جاز کیش موبائل نمبر:' : 'JazzCash Mobile Number:' }}</span>
                                            <div class="flex items-center justify-between bg-white border-2 border-rose-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                                <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="checkout_cod_jazzcash_num">{{ $settings->cod_advance_jazzcash_number }}</span>
                                                <button type="button" onclick="copyToClipboard('checkout_cod_jazzcash_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                    📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($settings->cod_advance_method === 'bank')
                                    <div class="space-y-4 text-sm text-slate-800">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'ادائیگی کا طریقہ:' : 'Payment Method:' }}</span>
                                            <span class="font-black text-blue-700 bg-blue-55 px-3 py-1 rounded-xl border border-blue-250 text-xs">Bank Transfer 🏦</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'بینک کا نام:' : 'Bank Name:' }}</span>
                                            <span class="font-black text-slate-950 text-sm md:text-base">{{ $settings->cod_advance_bank_name }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                            <span class="font-black text-slate-900 text-sm md:text-base">{{ $settings->cod_advance_account_title }}</span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                            <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'اکاؤنٹ نمبر / IBAN:' : 'Account Number / IBAN:' }}</span>
                                            <div class="flex items-center justify-between bg-white border-2 border-blue-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                                <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="checkout_cod_bank_num">{{ $settings->cod_advance_account_number }}</span>
                                                <button type="button" onclick="copyToClipboard('checkout_cod_bank_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                    📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <p class="text-xs text-amber-800 font-bold pt-3 border-t border-amber-200/40 leading-normal flex gap-1.5 items-center justify-center">
                                    <span>💬</span>
                                    <span>{{ $settings->enable_rtl ? 'ادائیگی کے بعد ٹرانزیکشن کا سکرین شاٹ واٹس ایپ پر ضرور شیئر کریں۔' : 'Please share the transaction screenshot on WhatsApp after payment.' }}</span>
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Place Order Button -->
                        <button type="submit" id="submitOrderBtn" 
                            class="w-full btn-primary-custom text-white font-black py-4.5 rounded-2xl text-lg transition shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>{{ $settings->enable_rtl ? 'آرڈر کنفرم کریں (Place Order) 🚀' : 'Confirm & Place Order 🚀' }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Order Summary Card (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-150/80 shadow-xl p-8 sticky top-24 space-y-6">
                    <h2 class="text-xl font-black text-gray-900 pb-4 border-b border-gray-100 flex justify-between items-center">
                        <span>{{ $settings->enable_rtl ? 'آرڈر کی تفصیل' : 'Order Summary' }}</span>
                        <span id="items-count-badge" class="bg-gray-100 text-gray-800 font-extrabold text-xs px-2.5 py-1 rounded-full">0 Items</span>
                    </h2>

                    <!-- Items List -->
                    <div id="checkoutItemsList" class="divide-y divide-gray-50 max-h-[360px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Loaded dynamically via JavaScript -->
                    </div>

                    <!-- Money Totals -->
                    <div class="pt-6 border-t border-gray-100 space-y-3.5">
                        <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                            <span>{{ $settings->enable_rtl ? 'اصل قیمت (Original Subtotal)' : 'Original Subtotal' }}</span>
                            <span id="checkoutOriginalSubtotal" class="font-extrabold text-gray-950">Rs. 0</span>
                        </div>
                        <div id="checkoutDiscountRow" class="flex justify-between items-center text-sm font-bold text-rose-600" style="display: none;">
                            <span>{{ $settings->enable_rtl ? 'خصوصی ڈسکاؤنٹ (Discount)' : 'Special Discount' }}</span>
                            <span id="checkoutDiscountAmount" class="font-black">- Rs. 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                            <span>{{ $settings->enable_rtl ? 'رقم (Subtotal)' : 'Subtotal' }}</span>
                            <span id="checkoutSubtotal" class="font-extrabold text-gray-950">Rs. 0</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                            <span>{{ $settings->enable_rtl ? 'ڈیلیوری چارجز (Shipping)' : 'Shipping Fee' }}</span>
                            <span id="checkoutShippingFee" class="font-extrabold text-gray-950">Rs. 0</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 text-base font-bold text-gray-900">
                            <span class="text-lg font-black">{{ $settings->enable_rtl ? 'کل رقم (Total)' : 'Total Amount' }}</span>
                            <span id="checkoutTotal" class="text-2xl font-black text-green-600">Rs. 200</span>
                        </div>
                        <div id="checkoutAdvanceRow" class="flex justify-between items-center text-xs font-bold text-amber-700 bg-amber-50 p-2.5 rounded-xl border border-amber-200" style="display: none;">
                            <span>{{ $settings->enable_rtl ? 'پیشگی ادائیگی (COD Advance Required)' : 'COD Advance Required' }}</span>
                            <span id="checkoutAdvanceAmount" class="font-extrabold text-amber-900">Rs. 0</span>
                        </div>
                        <div id="checkoutRemainingRow" class="flex justify-between items-center text-xs font-bold text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-200" style="display: none;">
                            <span>{{ $settings->enable_rtl ? 'بقیہ رقم (Remaining COD Balance)' : 'Remaining COD Balance' }}</span>
                            <span id="checkoutRemainingAmount" class="font-extrabold text-slate-900">Rs. 0</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs font-bold text-gray-400 mt-auto">
        &copy; {{ date('Y') }}, {{ $settings->footer_copyright ?? strtoupper($tenantId) . ' All rights reserved' }}
    </footer>

    <!-- JavaScript local storage payload binding -->
    <script>
        const shippingMode = "{{ $settings->shipping_mode ?? 'conditional' }}";
        const shippingFlatFee = {{ (int)($settings->shipping_flat_fee ?? 250) }};
        const shippingThreshold = {{ (int)($settings->shipping_threshold ?? 2000) }};

        const codRequireAdvance = {{ $settings->cod_require_advance ? 'true' : 'false' }};
        const codAdvanceType = "{{ $settings->cod_advance_type ?? 'flat' }}";
        const codAdvanceValue = {{ (float)($settings->cod_advance_value ?? 0) }};

        function switchPaymentMethod(method) {
            // Style active/inactive cards
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('border-green-600', 'bg-green-50/10');
                card.classList.add('border-gray-200');
            });
            const activeCard = document.getElementById('card-payment-' + method);
            if (activeCard) {
                activeCard.classList.remove('border-gray-200');
                activeCard.classList.add('border-green-600', 'bg-green-50/10');
            }

            // Show/hide details panels
            document.querySelectorAll('.payment-details-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const activePanel = document.getElementById('details-payment-' + method);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            // Update advance vs remaining breakdown
            updateTotalsBreakdown(method);
        }

        function updateTotalsBreakdown(method) {
            const advanceRow = document.getElementById('checkoutAdvanceRow');
            const remainingRow = document.getElementById('checkoutRemainingRow');
            if (!advanceRow || !remainingRow) return;

            // Get current total from UI
            const totalText = document.getElementById('checkoutTotal').innerText;
            const grandTotal = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;

            if (method === 'cod' && codRequireAdvance && codAdvanceValue > 0) {
                let advance = 0;
                if (codAdvanceType === 'flat') {
                    advance = Math.min(codAdvanceValue, grandTotal);
                } else if (codAdvanceType === 'percentage') {
                    advance = Math.round((grandTotal * codAdvanceValue) / 100);
                }
                const remaining = grandTotal - advance;

                document.getElementById('checkoutAdvanceAmount').innerText = 'Rs. ' + advance.toLocaleString();
                document.getElementById('checkoutRemainingAmount').innerText = 'Rs. ' + remaining.toLocaleString();

                const panelAmountEl = document.getElementById('cod-advance-panel-amount');
                if (panelAmountEl) {
                    panelAmountEl.innerText = 'Rs. ' + advance.toLocaleString();
                }

                advanceRow.style.display = 'flex';
                remainingRow.style.display = 'flex';
            } else {
                advanceRow.style.display = 'none';
                remainingRow.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem('cart') || '[]');
            } catch(e) {
                cart = [];
            }

            // Bind JSON content to hidden input
            const inputJson = document.getElementById('cart_items_json');
            if(inputJson) {
                inputJson.value = JSON.stringify(cart);
            }

            const itemsContainer = document.getElementById('checkoutItemsList');
            const subtotalEl = document.getElementById('checkoutSubtotal');
            const totalEl = document.getElementById('checkoutTotal');
            const countBadge = document.getElementById('items-count-badge');
            const submitBtn = document.getElementById('submitOrderBtn');

            if (!itemsContainer) return;

            if (cart.length === 0) {
                itemsContainer.innerHTML = `
                    <div class="text-center py-10 text-gray-400 font-bold space-y-3">
                        <span class="text-4xl block">🛒</span>
                        <p class="text-sm">{{ $settings->enable_rtl ? 'آپ کا کارٹ خالی ہے!' : 'Your cart is empty!' }}</p>
                        <a href="/collection" class="inline-block bg-green-50 text-green-700 border border-green-250 text-xs px-4 py-2 rounded-xl font-extrabold hover:bg-green-100 transition">{{ $settings->enable_rtl ? 'شاپنگ کریں' : 'Return to Shop' }}</a>
                    </div>
                `;
                if(submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.remove('hover:bg-green-700');
                }
                return;
            }

            // Render list
            itemsContainer.innerHTML = '';
            let originalSubtotal = 0;
            let subtotal = 0;
            let totalCount = 0;

            cart.forEach((item) => {
                const itemOrigPrice = item.originalPrice || item.price;
                originalSubtotal += itemOrigPrice * item.qty;
                subtotal += item.price * item.qty;
                totalCount += item.qty;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex gap-4 py-4 items-center';

                const imageHtml = item.image 
                    ? `<img src="${item.image.startsWith('http') || item.image.startsWith('/') ? item.image : '/storage/' + item.image}" class="w-12 h-12 rounded-lg object-cover border bg-gray-50">`
                    : `<div class="w-12 h-12 bg-gray-50 rounded-lg border flex items-center justify-center text-[8px] text-gray-400 font-bold">No Image</div>`;

                let variantsHtml = '';
                if (item.selectedVariants && Object.keys(item.selectedVariants).length > 0) {
                    variantsHtml = '<div class="text-[10px] text-gray-500 font-bold mt-1 flex flex-wrap gap-1">';
                    for (let [key, val] of Object.entries(item.selectedVariants)) {
                        variantsHtml += `<span class="inline-block bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200/60">${key}: ${val}</span>`;
                    }
                    variantsHtml += '</div>';
                }

                const displayPrice = item.originalPrice || item.price;
                itemDiv.innerHTML = `
                    ${imageHtml}
                    <div class="flex-grow">
                        <h4 class="font-extrabold text-gray-800 text-sm leading-normal">${item.name}</h4>
                        ${variantsHtml}
                        <span class="text-xs text-gray-400 font-bold">Qty: ${item.qty} &times; Rs. ${displayPrice}</span>
                    </div>
                    <span class="text-sm font-extrabold text-gray-900 shrink-0">Rs. ${displayPrice * item.qty}</span>
                `;
                itemsContainer.appendChild(itemDiv);
            });

            // Update badge, subtotal, and total
            countBadge.innerText = `${totalCount} ${totalCount === 1 ? 'Item' : 'Items'}`;
            
            const discount = originalSubtotal - subtotal;
            document.getElementById('checkoutOriginalSubtotal').innerText = 'Rs. ' + originalSubtotal.toLocaleString();
            
            if (discount > 0) {
                document.getElementById('checkoutDiscountRow').style.display = 'flex';
                document.getElementById('checkoutDiscountAmount').innerText = '- Rs. ' + discount.toLocaleString();
            } else {
                document.getElementById('checkoutDiscountRow').style.display = 'none';
            }
            
            subtotalEl.innerText = 'Rs. ' + subtotal.toLocaleString();
            
            let shipping = 0;
            if (shippingMode === 'flat') {
                shipping = shippingFlatFee;
            } else if (shippingMode === 'conditional') {
                if (subtotal >= shippingThreshold) {
                    shipping = 0;
                } else {
                    shipping = shippingFlatFee;
                }
            } else {
                shipping = 0; // free delivery
            }

            const shippingFeeEl = document.getElementById('checkoutShippingFee');
            if (shippingFeeEl) {
                if (shipping === 0) {
                    shippingFeeEl.innerText = "{{ $settings->enable_rtl ? 'مفت (Free)' : 'Free' }}";
                } else {
                    shippingFeeEl.innerText = 'Rs. ' + shipping.toLocaleString();
                }
            }
            totalEl.innerText = 'Rs. ' + (subtotal + shipping).toLocaleString();

            // Initialize payment method preview
            const checkedMethodInput = document.querySelector('input[name="payment_method"]:checked');
            if (checkedMethodInput) {
                switchPaymentMethod(checkedMethodInput.value);
            }

            // Fetch public IP details using multiple fallbacks (bypassing strict browser CORS/adblockers)
            const ipEndpoints = [
                'https://api.ipify.org?format=json',
                'https://api64.ipify.org?format=json',
                'https://ipapi.co/json/'
            ];

            function fetchClientIP(index) {
                if (index >= ipEndpoints.length) return;
                fetch(ipEndpoints[index])
                    .then(res => res.json())
                    .then(data => {
                        const ip = data.ip;
                        if (ip) {
                            document.getElementById('client_ip').value = ip;
                            
                            // If the endpoint also provides city/geo (like ipapi)
                            if (data.city) {
                                document.getElementById('ip_city').value = data.city || '';
                                document.getElementById('ip_country').value = data.country_name || '';
                                document.getElementById('ip_isp').value = data.org || '';
                                if (!document.getElementById('latitude').value) {
                                    document.getElementById('latitude').value = data.latitude || '';
                                }
                                if (!document.getElementById('longitude').value) {
                                    document.getElementById('longitude').value = data.longitude || '';
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.warn(`IP endpoint ${ipEndpoints[index]} failed, trying next...`);
                        fetchClientIP(index + 1);
                    });
            }

            fetchClientIP(0);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    console.log("GPS Location lookup failed or denied.");
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            }
        });

        function copyToClipboard(id) {
            const element = document.getElementById(id);
            if (!element) return;
            const text = element.innerText;
            const container = element.parentElement;
            const button = container ? container.querySelector('button') : null;
            const originalHTML = button ? button.innerHTML : '';

            navigator.clipboard.writeText(text).then(() => {
                if (button) {
                    button.innerHTML = "✅ {{ $settings->enable_rtl ? 'کاپی ہو گیا' : 'Copied' }}";
                    button.classList.remove('text-indigo-650', 'bg-slate-50');
                    button.classList.add('text-green-650', 'bg-green-50', 'border-green-200');
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('text-green-650', 'bg-green-50', 'border-green-200');
                        button.classList.add('text-indigo-650', 'bg-slate-50');
                    }, 2000);
                }
            }).catch(err => {
                const tempInput = document.createElement("input");
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                if (button) {
                    button.innerHTML = "✅ {{ $settings->enable_rtl ? 'کاپی ہو گیا' : 'Copied' }}";
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                    }, 2000);
                }
            });
        }
    </script>
</body>
</html>
