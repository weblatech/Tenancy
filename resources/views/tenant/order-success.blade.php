<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->enable_rtl ? 'آرڈر کامیابی سے موصول ہو گیا' : 'Order Placed Successfully' }} - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/dist/umd/supabase.min.js"></script>
    
    @if(!empty($settings->facebook_pixel_id))
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings->facebook_pixel_id }}');
        fbq('track', 'Purchase', { value: {{ $order->total }}, currency: 'PKR' });
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
            <a href="{{ tenant_store_url('/') }}">
                @if($settings && $settings->header_logo)
                    <img class="w-auto object-contain store-logo-img" src="{{ tenant_asset($settings->header_logo) }}" alt="Store Logo">
                @else
                    <span class="text-2xl font-black text-gray-900 tracking-tight">🛍️ {{ strtoupper($tenantId) }}</span>
                @endif
            </a>
            <a href="{{ tenant_store_url('/') }}" class="text-sm font-bold text-green-600 hover:underline flex items-center gap-1">
                {{ $settings->enable_rtl ? 'ہوم پیج پر جائیں' : 'Go to Homepage' }}
            </a>
        </div>
    </header>

    <!-- Main Success Receipt Area -->
    <main class="max-w-3xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16 flex-grow">
        <div class="bg-white rounded-[2.5rem] border border-gray-150/80 shadow-2xl p-8 md:p-12 text-center space-y-8">
            
            <!-- Success Green Icon -->
            <div class="w-20 h-20 bg-green-50 text-green-600 border border-green-200 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner">
                ✓
            </div>

            <!-- Confirmation Title & Order ID -->
            <div>
                <h1 class="text-3xl md:text-4xl font-black text-gray-950 mb-3 tracking-tight">
                    {{ $settings->enable_rtl ? 'آپ کا آرڈر کامیابی سے موصول ہو گیا ہے! 🎉' : 'Thank You for Your Order! 🎉' }}
                </h1>
                <p class="text-gray-500 font-bold text-base">
                    {{ $settings->enable_rtl ? 'آپ کا آرڈر نمبر یہ ہے:' : 'Your Order Reference ID is:' }} 
                    <span class="text-gray-900 font-black" style="font-family: sans-serif;">#{{ $order->id }}</span>
                </p>
                <p class="text-sm text-green-700 font-bold mt-2 bg-green-50 inline-block px-4 py-1.5 rounded-full border border-green-150">
                    {{ $settings->enable_rtl ? 'ہمارا نمائندہ جلد ہی آپ سے رابطہ کرے گا۔' : 'Our agent will contact you shortly to confirm the order.' }}
                </p>
            </div>

            <!-- WhatsApp Confirmation Prompt -->
            @if(!empty($settings->footer_whatsapp) || !empty($settings->footer_phone))
                @php
                    $merchantPhone = preg_replace('/[^0-9]/', '', $settings->footer_whatsapp ?? $settings->footer_phone ?? '');
                    if (str_starts_with($merchantPhone, '03')) {
                        $merchantPhone = '92' . substr($merchantPhone, 1);
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
                        $advanceText = "*COD Advance / ایڈوانس پیمنٹ:* *Rs. " . number_format($order->cod_advance_required) . "*\n"
                                     . "*Doorstep COD / بقیہ رقم:* *Rs. " . number_format($order->total - $order->cod_advance_required) . "*\n"
                                     . "-----------------------------------\n";
                    }

                    $customerMsgText = "*ORDER VERIFICATION / آرڈر کی تصدیق* 🛍️\n"
                                     . "-----------------------------------\n"
                                     . "السلام علیکم! میں اپنے آرڈر کی تصدیق کرنا چاہتا/چاہتی ہوں۔\n"
                                     . "Hello! I would like to confirm my order.\n\n"
                                     . "*Order ID / آرڈر نمبر:* #{$order->id}\n"
                                     . "*Date / تاریخ:* {$orderDate}\n"
                                     . "-----------------------------------\n"
                                     . "*Order Items / آرڈر کی تفصیل:*\n"
                                     . $itemsList
                                     . "-----------------------------------\n"
                                     . "*Total Amount / کل رقم:* *Rs. " . number_format($order->total) . "*\n"
                                     . "-----------------------------------\n"
                                     . $advanceText
                                     . "*Customer Shipping Details / کسٹمر کی تفصیلات:*\n"
                                     . "*Name:* {$order->customer_name}\n"
                                     . "*Phone:* {$order->customer_phone}\n"
                                     . "*Address:* {$order->customer_address}, {$order->customer_city}\n"
                                     . "-----------------------------------\n"
                                     . "Please ship my order as soon as possible. Thank you!\n"
                                     . "برائے مہربانی میرا آرڈر جلد از جلد روانہ کریں۔ شکریہ!";

                    $customerWaLink = "https://wa.me/{$merchantPhone}?text=" . urlencode($customerMsgText);
                @endphp

                <div class="bg-gradient-to-br from-emerald-50 to-teal-50/30 border border-emerald-200/80 rounded-3xl p-6 md:p-8 text-center space-y-4 shadow-sm">
                    <div class="flex items-center justify-center gap-2 text-emerald-600 font-extrabold text-lg md:text-xl">
                        <span class="text-2xl">💬</span>
                        <span>{{ $settings->enable_rtl ? 'واٹس ایپ پر آرڈر کی تصدیق کریں' : 'Confirm Order on WhatsApp' }}</span>
                    </div>
                    <p class="text-xs md:text-sm font-bold text-slate-600 leading-relaxed max-w-xl mx-auto">
                        {{ $settings->enable_rtl ? 'اپنے آرڈر کو فوری پروسیس کروانے کے لیے نیچے دیے گئے بٹن پر کلک کر کے واٹس ایپ پر تصدیقی پیغام بھیجیں۔' : 'To process your order instantly, click the button below to send a quick verification message on WhatsApp.' }}
                    </p>
                    <p id="whatsapp-redirect-status" class="text-[10px] font-bold text-emerald-600 hidden animate-pulse">
                        {{ $settings->enable_rtl ? 'واٹس ایپ پر ری ڈائریکٹ کیا جا رہا ہے...' : 'Auto-redirecting to WhatsApp in a moment...' }}
                    </p>
                    <a href="{{ $customerWaLink }}" id="whatsapp-confirm-btn" target="_blank" class="inline-flex items-center justify-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 px-8 rounded-2xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/35 transition-all transform hover:-translate-y-0.5 duration-200 text-sm w-full sm:w-auto">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.035-3.376c1.614.957 3.524 1.463 5.485 1.464 5.769 0 10.463-4.69 10.467-10.46 0-2.8-.1.087-5.419-4.832-1.96-1.957-4.566-3.036-7.34-3.037-5.772 0-10.471 4.693-10.475 10.463-.001 1.955.51 3.86 1.478 5.498l-.979 3.579 3.673-.963zm11.593-5.26c-.302-.151-1.787-.881-2.062-.981-.275-.1-.475-.151-.675.151-.2.302-.775.981-.95 1.18-.175.2-.35.225-.65.075-.302-.151-1.272-.469-2.423-1.496-.895-.798-1.5-1.784-1.675-2.085-.175-.302-.019-.465.132-.614.135-.133.302-.352.453-.528.151-.175.2-.302.302-.503.1-.2.05-.377-.025-.528-.075-.151-.675-1.628-.925-2.229-.244-.588-.491-.508-.675-.518-.174-.01-.375-.012-.575-.012-.2 0-.525.075-.8.376-.275.302-1.05 1.03-1.05 2.512 0 1.48 1.075 2.912 1.225 3.112.15.2 2.115 3.23 5.123 4.527.715.308 1.273.493 1.708.631.714.227 1.365.195 1.879.119.573-.086 1.787-.73 2.037-1.432.25-.702.25-1.303.175-1.43-.075-.128-.275-.203-.575-.353z"/>
                        </svg>
                        <span>{{ $settings->enable_rtl ? 'تصدیق بھیجیں' : 'Send Confirmation' }}</span>
                    </a>
                </div>
            @endif

            <!-- Customer Details Summary -->
            <div class="border-t border-b border-gray-100 py-6 text-right md:text-left grid grid-cols-1 md:grid-cols-2 gap-6" style="text-align: inherit;">
                <div class="space-y-2.5">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ $settings->enable_rtl ? 'شپنگ تفصیلات' : 'Shipping Details' }}</h4>
                    <p class="text-base font-black text-gray-900 leading-normal">{{ $order->customer_name }}</p>
                    <p class="text-sm font-bold text-gray-500" style="direction: ltr; text-align: inherit;">{{ $order->customer_phone }}</p>
                    <p class="text-sm font-bold text-gray-500 leading-relaxed">{{ $order->customer_address }}, {{ $order->customer_city }}</p>
                </div>
                <div class="space-y-2.5 md:border-l md:border-gray-100 md:pl-6 {{ $settings->enable_rtl ? 'md:border-r md:border-l-0 md:pr-6 md:pl-0' : '' }}">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">{{ $settings->enable_rtl ? 'طریقہ ادائیگی' : 'Payment Method' }}</h4>
                    <p class="text-base font-black text-gray-900 leading-normal">
                        @if($order->payment_method === 'cod')
                            {{ $settings->enable_rtl ? 'کیش آن ڈیلیوری (COD)' : 'Cash on Delivery (COD)' }}
                        @elseif($order->payment_method === 'bank')
                            {{ $settings->enable_rtl ? 'بینک ٹرانسفر (Bank Transfer)' : 'Bank Account Transfer' }}
                        @elseif($order->payment_method === 'easypaisa')
                            {{ $settings->enable_rtl ? 'ایزی پیسہ (EasyPaisa)' : 'EasyPaisa Account' }}
                        @elseif($order->payment_method === 'jazzcash')
                            {{ $settings->enable_rtl ? 'جاز کیش (JazzCash)' : 'JazzCash Account' }}
                        @else
                            {{ strtoupper($order->payment_method ?? 'COD') }}
                        @endif
                    </p>
                    <div class="text-sm md:text-base font-extrabold mt-2 leading-normal">
                        @if($order->payment_method === 'cod')
                            @if($order->cod_advance_required > 0)
                                <div class="bg-white border-2 border-amber-200 p-6 rounded-[2.5rem] text-left {{ $settings->enable_rtl ? 'text-right' : '' }} space-y-5 shadow-lg">
                                    <p class="font-black text-slate-900 text-lg flex items-center gap-2 border-b border-gray-100 pb-3 leading-normal justify-center md:justify-start">
                                        ⚠️ {{ $settings->enable_rtl ? 'اس آرڈر کے لیے پیشگی ادائیگی درکار ہے!' : 'Advance Payment Required!' }}
                                    </p>
                                    
                                    <!-- Beautiful Warning Alert -->
                                    <div class="bg-red-50 border border-red-200 text-red-900 p-4.5 rounded-2xl text-xs md:text-sm font-bold leading-relaxed flex items-start gap-3 shadow-sm">
                                        <span class="text-xl shrink-0">⚠️</span>
                                        <div class="space-y-1 w-full text-center md:text-left {{ $settings->enable_rtl ? 'md:text-right' : '' }}">
                                            <p class="font-black text-base text-red-700 leading-snug">
                                                {{ $settings->enable_rtl ? 'اہم نوٹ: جب تک ایڈوانس پیمنٹ نہیں کی جائے گی آپ کا آرڈر پروسس نہیں کیا جائے گا۔' : 'Important: Until the advance payment is received, your order will NOT be processed.' }}
                                            </p>
                                            <p class="text-xs text-red-650 font-semibold leading-normal">
                                                {{ $settings->enable_rtl ? 'Important: Until the advance payment is received, your order will NOT be processed.' : 'اہم نوٹ: جب تک ایڈوانس پیمنٹ نہیں کی جائے گی آپ کا آرڈر پروسس نہیں کیا جائے گا۔' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Required Advance Amount Card -->
                                    <div class="bg-gradient-to-r from-amber-50 to-amber-50/50 border border-amber-200 p-4.5 rounded-2xl text-center space-y-1.5 shadow-sm">
                                        <span class="text-xs text-slate-500 font-extrabold block uppercase tracking-wider">
                                            {{ $settings->enable_rtl ? 'پیشگی ادائیگی کی رقم (Required Advance Amount)' : 'Required Advance Amount / پیشگی ادائیگی کی رقم' }}
                                        </span>
                                        <div class="text-2xl md:text-3xl font-black text-green-700 tracking-tight">
                                            Rs. {{ number_format($order->cod_advance_required) }}
                                        </div>
                                    </div>

                                    <div class="text-xs font-bold text-amber-900 space-y-2 leading-relaxed">
                                        <p class="flex items-center justify-between gap-4 border-b border-amber-100 pb-1.5">
                                            <span>{{ $settings->enable_rtl ? 'پیشگی ادائیگی (Advance Required):' : 'Advance Payment Required:' }}</span>
                                            <span class="text-slate-900 font                                    <!-- Structured Deposit Account Details -->
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

                                    <div class="{{ $codPanelBg }} p-6 rounded-[2rem] border-2 space-y-5 shadow-sm mt-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">
                                        <span class="text-sm text-slate-700 font-black block uppercase tracking-wider border-b border-gray-255 pb-2.5 flex justify-between items-center">
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
                                                        <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="cod_adv_easypaisa_num">{{ $settings->cod_advance_easypaisa_number }}</span>
                                                        <button type="button" onclick="copyToClipboard('cod_adv_easypaisa_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($settings->cod_advance_method === 'jazzcash')
                                            <div class="space-y-4 text-sm text-slate-800">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-250/60 pb-3 gap-2">
                                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'ادائیگی کا طریقہ:' : 'Payment Method:' }}</span>
                                                    <span class="font-black text-red-705 bg-rose-55 px-3 py-1 rounded-xl border border-rose-250 text-xs">JazzCash 📱</span>
                                                </div>
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-255/60 pb-3 gap-2">
                                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                                    <span class="font-black text-slate-900 text-sm md:text-base">{{ $settings->cod_advance_jazzcash_title }}</span>
                                                </div>
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'جاز کیش موبائل نمبر:' : 'JazzCash Mobile Number:' }}</span>
                                                    <div class="flex items-center justify-between bg-white border-2 border-rose-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                                        <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="cod_adv_jazzcash_num">{{ $settings->cod_advance_jazzcash_number }}</span>
                                                        <button type="button" onclick="copyToClipboard('cod_adv_jazzcash_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($settings->cod_advance_method === 'bank')
                                            <div class="space-y-4 text-sm text-slate-800">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-250/60 pb-3 gap-2">
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
                                                        <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="cod_adv_bank_num">{{ $settings->cod_advance_account_number }}</span>
                                                        <button type="button" onclick="copyToClipboard('cod_adv_bank_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 font-bold pt-3 border-t border-gray-100 leading-normal flex gap-1.5 items-center justify-center">
                                        <span>💬</span>
                                        <span>{{ $settings->enable_rtl ? 'رقم بھیجنے کے بعد سکرین شاٹ واٹس ایپ کرنا لازمی ہے۔' : 'Please send the screenshot on WhatsApp after paying.' }}</span>
                                    </p>
                                </div>
                            @else
                                <p class="text-slate-700 bg-amber-50 border border-amber-250 px-4 py-3 rounded-xl font-bold">
                                    {{ $settings->enable_rtl ? 'رقم کی ادائیگی آرڈر وصول کرتے وقت کریں۔' : 'Pay in cash when you receive the package.' }}
                                </p>
                            @endif
                        @elseif($order->payment_method === 'bank')
                            <div class="bg-blue-50/40 text-slate-800 p-6 md:p-8 rounded-3xl border-2 border-blue-400 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} mt-3 space-y-4 shadow-sm">
                                <h4 class="font-black text-blue-900 text-lg md:text-xl flex items-center gap-2 border-b border-blue-200 pb-3 leading-normal justify-center sm:justify-start">
                                    🏦 {{ $settings->enable_rtl ? 'بینک ٹرانسفر کی تفصیلات' : 'Bank Transfer Details' }}
                                </h4>
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'بینک کا نام:' : 'Bank Name:' }}</span>
                                    <span class="text-slate-955 font-black text-base md:text-lg">{{ $settings->payment_bank_name }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-955 font-black text-base md:text-lg">{{ $settings->payment_bank_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'اکاؤنٹ نمبر / IBAN:' : 'Account Number / IBAN:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-blue-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-950 font-black text-lg md:text-xl select-all tracking-wider" id="direct_bank_num">{{ $settings->payment_bank_number }}</span>
                                        <button type="button" onclick="copyToClipboard('direct_bank_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                        @elseif($order->payment_method === 'easypaisa')
                            <div class="bg-emerald-50/40 text-slate-800 p-6 md:p-8 rounded-3xl border-2 border-emerald-400 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} mt-3 space-y-4 shadow-sm">
                                <h4 class="font-black text-emerald-900 text-lg md:text-xl flex items-center gap-2 border-b border-emerald-200 pb-3 leading-normal justify-center sm:justify-start">
                                    📱 {{ $settings->enable_rtl ? 'ایزی پیسہ اکاؤنٹ کی تفصیلات' : 'EasyPaisa Details' }}
                                </h4>
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ ہولڈر کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-955 font-black text-base md:text-lg">{{ $settings->payment_easypaisa_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'ایزی پیسہ نمبر:' : 'EasyPaisa Number:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-emerald-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="direct_easypaisa_num">{{ $settings->payment_easypaisa_number }}</span>
                                        <button type="button" onclick="copyToClipboard('direct_easypaisa_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                        @elseif($order->payment_method === 'jazzcash')
                            <div class="bg-rose-50/40 text-slate-800 p-6 md:p-8 rounded-3xl border-2 border-rose-450 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} mt-3 space-y-4 shadow-sm">
                                <h4 class="font-black text-rose-900 text-lg md:text-xl flex items-center gap-2 border-b border-rose-200 pb-3 leading-normal justify-center sm:justify-start">
                                    📱 {{ $settings->enable_rtl ? 'جاز کیش اکاؤنٹ کی تفصیلات' : 'JazzCash Details' }}
                                </h4>
                                <p class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/60 pb-2.5">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base">{{ $settings->enable_rtl ? 'اکاؤنٹ ہولڈر کا نام:' : 'Account Title:' }}</span>
                                    <span class="text-slate-955 font-black text-base md:text-lg">{{ $settings->payment_jazzcash_title }}</span>
                                </p>
                                <p class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                    <span class="text-slate-600 font-extrabold text-sm md:text-base shrink-0">{{ $settings->enable_rtl ? 'جاز کیش نمبر:' : 'JazzCash Number:' }}</span>
                                    <div class="flex items-center justify-between bg-white border-2 border-rose-300 rounded-2xl pl-4 pr-2.5 py-2.5 shadow-sm w-full sm:w-auto min-w-[240px]">
                                        <span class="font-mono text-slate-955 font-black text-lg md:text-xl select-all tracking-wider" id="direct_jazzcash_num">{{ $settings->payment_jazzcash_number }}</span>
                                        <button type="button" onclick="copyToClipboard('direct_jazzcash_num')" class="text-xs font-black text-indigo-700 hover:text-indigo-900 transition flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-250 px-3 py-1.5 rounded-xl shadow-sm shrink-0">
                                            📋 <span>{{ $settings->enable_rtl ? 'کاپی' : 'Copy' }}</span>
                                        </button>
                                    </div>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Receipt Table -->
            <div class="space-y-4">
                <h3 class="text-lg font-black text-gray-900 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">
                    {{ $settings->enable_rtl ? 'آرڈر کی تفصیلات' : 'Order Items' }}
                </h3>
                <div class="border border-gray-100 rounded-2xl overflow-hidden bg-gray-50/50">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100/70 text-gray-700 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3.5 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">{{ $settings->enable_rtl ? 'پروڈکٹ' : 'Product' }}</th>
                                <th class="px-5 py-3.5 text-center">{{ $settings->enable_rtl ? 'تعداد' : 'Qty' }}</th>
                                <th class="px-5 py-3.5 text-right {{ $settings->enable_rtl ? 'text-left' : '' }}">{{ $settings->enable_rtl ? 'رقم' : 'Total' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            @foreach($order->cart_items as $item)
                                <tr>
                                    <td class="px-5 py-4 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} font-black text-gray-900">
                                        <div class="flex flex-col">
                                            <span>{{ $item['name'] }}</span>
                                            @if(!empty($item['selectedVariants']) && is_array($item['selectedVariants']))
                                                <div class="flex flex-wrap gap-1 mt-1 text-[10px] text-gray-500 font-bold">
                                                    @foreach($item['selectedVariants'] as $optKey => $optVal)
                                                        <span class="inline-block bg-gray-150 border border-gray-200 px-1.5 py-0.5 rounded">{{ $optKey }}: {{ $optVal }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center text-gray-700" style="font-family: sans-serif;">
                                        {{ $item['qty'] }}
                                    </td>
                                    <td class="px-5 py-4 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} font-bold text-gray-900" style="font-family: sans-serif;">
                                        Rs. {{ number_format(($item['originalPrice'] ?? $item['price']) * $item['qty']) }}
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Subtotal, Shipping, Total -->
                            @php
                                $origSubtotal = 0;
                                foreach($order->cart_items as $item) {
                                    $origPrice = (float)($item['originalPrice'] ?? $item['price'] ?? 0);
                                    $origSubtotal += $origPrice * (int)($item['qty'] ?? 1);
                                }
                                $discountAmount = $origSubtotal - $order->subtotal;
                            @endphp

                            <tr class="bg-gray-50/70 font-bold text-gray-600">
                                <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">{{ $settings->enable_rtl ? 'اصل قیمت (Original Subtotal)' : 'Original Subtotal' }}</td>
                                <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} font-black text-gray-900" style="font-family: sans-serif;">Rs. {{ number_format($origSubtotal) }}</td>
                            </tr>
                            @if($discountAmount > 0)
                                <tr class="bg-gray-50/70 font-bold text-rose-600">
                                    <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">{{ $settings->enable_rtl ? 'خصوصی ڈسکاؤنٹ (Discount)' : 'Special Discount' }}</td>
                                    <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} font-black" style="font-family: sans-serif;">- Rs. {{ number_format($discountAmount) }}</td>
                                </tr>
                            @endif
                            <tr class="bg-gray-50/70 font-bold text-gray-600">
                                <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">{{ $settings->enable_rtl ? 'رقم (Subtotal)' : 'Subtotal' }}</td>
                                <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} font-black text-gray-900" style="font-family: sans-serif;">Rs. {{ number_format($order->subtotal) }}</td>
                            </tr>
                            <tr class="bg-gray-50/70 font-bold text-gray-600">
                                <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }}">{{ $settings->enable_rtl ? 'ڈیلیوری چارجز' : 'Shipping' }}</td>
                                <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} font-black text-gray-900" style="font-family: sans-serif;">Rs. {{ number_format($order->shipping_fee) }}</td>
                            </tr>
                            <tr class="bg-green-50/50 font-black text-green-800 border-t border-gray-200">
                                <td colspan="2" class="px-5 py-4 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} text-base">{{ $settings->enable_rtl ? 'کل رقم (Total)' : 'Total Amount' }}</td>
                                <td class="px-5 py-4 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} text-lg text-green-600" style="font-family: sans-serif;">Rs. {{ number_format($order->total) }}</td>
                            </tr>
                            @if($order->payment_method === 'cod' && $order->cod_advance_required > 0)
                            <tr class="bg-amber-50/70 font-black text-amber-900 border-t border-amber-200">
                                <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} text-xs">{{ $settings->enable_rtl ? 'پیشگی ادائیگی (COD Advance Required)' : 'COD Advance Required' }}</td>
                                <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} text-sm text-amber-700" style="font-family: sans-serif;">Rs. {{ number_format($order->cod_advance_required) }}</td>
                            </tr>
                            <tr class="bg-gray-100/60 font-black text-gray-800 border-t border-gray-200">
                                <td colspan="2" class="px-5 py-3 text-left {{ $settings->enable_rtl ? 'text-right' : '' }} text-xs">{{ $settings->enable_rtl ? 'بقیہ رقم (Remaining COD Balance)' : 'Remaining COD Balance' }}</td>
                                <td class="px-5 py-3 text-right {{ $settings->enable_rtl ? 'text-left' : '' }} text-sm text-gray-700" style="font-family: sans-serif;">Rs. {{ number_format($order->total - $order->cod_advance_required) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CTAs -->
            <div class="pt-6 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ tenant_store_url('/collection') }}" class="btn-primary-custom text-white font-black py-4 px-8 rounded-xl text-sm transition shadow-md">
                    {{ $settings->enable_rtl ? 'شاپنگ جاری رکھیں' : 'Continue Shopping' }}
                </a>
                <a href="{{ tenant_store_url('/') }}" class="btn-secondary-custom font-bold py-4 px-8 rounded-xl text-sm transition">
                    {{ $settings->enable_rtl ? 'ہوم پیج پر واپس جائیں' : 'Back to Home' }}
                </a>
            </div>

        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center text-xs font-bold text-gray-400 mt-auto">
        &copy; {{ date('Y') }}, {{ $settings->footer_copyright ?? strtoupper($tenantId) . ' All rights reserved' }}
    </footer>

    <!-- Clear local storage on load & auto redirect to WhatsApp -->
    <script>
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

        document.addEventListener('DOMContentLoaded', () => {
            localStorage.removeItem('cart');

            // Sync order to Supabase for WhatsApp CRM
            (async () => {
                try {
                    const SUPABASE_URL = 'https://zwdumolledeoxlvqckka.supabase.co';
                    const SUPABASE_KEY = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';
                    const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

                    const orderData = {
                        id: {{ $order->id }},
                        customer: @json($order->customer_name),
                        mobile: @json($order->customer_phone),
                        status: 'Unfulfilled',
                        price: {{ $order->total ?? 0 }},
                    };

                    console.log('[ThankYou] Syncing order to Supabase:', orderData);

                    const { data, error } = await supabase
                        .from('orders')
                        .upsert(orderData, { onConflict: 'id' });

                    if (error) {
                        console.error('[ThankYou] Supabase sync error:', error);
                    } else {
                        console.log('[ThankYou] Order synced to Supabase successfully');
                    }
                } catch (e) {
                    console.error('[ThankYou] Supabase sync failed:', e);
                }
            })();

            // Auto redirect to WhatsApp if configured
            const confirmBtn = document.getElementById('whatsapp-confirm-btn');
            const statusEl = document.getElementById('whatsapp-redirect-status');
            if (confirmBtn) {
                if (statusEl) statusEl.classList.remove('hidden');
                setTimeout(() => {
                    window.open(confirmBtn.href, '_blank');
                }, 1500);
            }
        });
    </script>
</body>
</html>
