<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment & Shipping Settings — {{ tenant('name') ?? strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var path = window.location.pathname;
        var parts = path.split('/').filter(Boolean);
        var tenantPrefix = parts.length > 0 && parts[0] !== 'shop' ? '/' + parts[0] : '';
        document.querySelectorAll('form[action^="/shop/"]').forEach(function(form) {
            form.setAttribute('action', tenantPrefix + form.getAttribute('action'));
        });
    });
    </script>
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
            border-radius: 28px;
            padding: 30px;
        }
        .card-premium:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.35);
            box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.06), 0 10px 15px -5px rgba(99, 102, 241, 0.02);
        }
        .input-premium-v2 {
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            font-size: 0.875rem; /* text-sm */
            font-weight: 700; /* font-bold */
            color: #0f172a; /* text-slate-900 */
            border-radius: 16px;
            padding: 14px 16px;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        .input-premium-v2:focus {
            border-color: #4f46e5; /* indigo-600 */
            box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.12);
        }
        .select-premium-v2 {
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
            border-radius: 16px;
            padding: 14px 16px;
            width: 100%;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 16px center;
            background-repeat: no-repeat;
            background-size: 18px;
            padding-right: 46px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        .select-premium-v2:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.12);
        }
        .collapsible-wrapper {
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        opacity 0.3s ease-out, 
                        margin 0.3s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .payment-fields-block {
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), 
                        opacity 0.3s ease-out, 
                        margin 0.3s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            margin-top: 0;
        }
        .payment-fields-block.active {
            max-height: 500px;
            opacity: 1;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased pb-32 relative overflow-x-hidden">
 
    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>

    <!-- Top Premium Navigation Bar -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md relative z-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 p-2.5 rounded-xl shadow-lg shadow-amber-500/25">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-amber-400 text-[10px] font-black block uppercase tracking-wider">Payment & Delivery Settings</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-slate-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-800">|</span>
                    <a href="{{ tenant_store_url() }}" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition flex items-center gap-1 px-3 py-2 rounded-xl hover:bg-slate-900">
                        <span>View Storefront ↗</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto mt-12 px-6">
        
        <!-- Page Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/60 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="/shop" class="text-[10px] font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm border border-indigo-100">
                        <span>← Back to Dashboard</span>
                    </a>
                    <span class="text-[10px] font-bold text-slate-400">/ Settings</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight bg-gradient-to-r from-slate-950 via-slate-900 to-indigo-950 bg-clip-text text-transparent">Payment & Delivery Configuration</h2>
                <p class="text-slate-500 font-semibold text-xs mt-1">Configure shipping modes, flat rates, and set up merchant payment receiving accounts.</p>
            </div>
            
            <!-- Store Indicator Badge -->
            <div class="flex gap-2 items-center">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 bg-slate-100 px-3.5 py-2 rounded-2xl border border-slate-200">
                    Store: {{ tenant('name') ?? $tenantId }}
                </span>
            </div>
        </div>

        <!-- Success Toast -->
        @if(session('success'))
            <div class="mb-10 bg-emerald-50 border border-emerald-200 text-emerald-950 px-6 py-4 rounded-3xl font-bold flex items-center gap-4 shadow-sm shadow-emerald-500/5">
                <div class="bg-emerald-500 text-white rounded-2xl p-2 shadow-sm shadow-emerald-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-extrabold text-slate-950 block">Settings Saved Successfully</span>
                    <span class="text-xs font-bold text-emerald-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form action="/shop/payments" method="POST" id="paymentSettingsForm" class="space-y-10">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column: Shipping & COD (5 Columns) -->
                <div class="lg:col-span-5 space-y-8">
                    
                    <!-- Card 1: Shipping Rates & Modes -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center gap-4 mb-8 pb-5 border-b border-slate-100">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-650 flex items-center justify-center text-white shadow-md shadow-blue-500/25">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base">Delivery & Shipping</h3>
                                <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Set shipping rates and delivery threshold</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                    <span>Shipping Mode</span>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">طریقہ کار</span>
                                </label>
                                <select name="shipping_mode" id="shipping_mode" onchange="toggleShippingFields()" class="select-premium-v2">
                                    <option value="free" {{ ($settings->shipping_mode ?? 'conditional') == 'free' ? 'selected' : '' }}>Free Delivery on All Orders</option>
                                    <option value="flat" {{ ($settings->shipping_mode ?? 'conditional') == 'flat' ? 'selected' : '' }}>Flat Rate Delivery Charge</option>
                                    <option value="conditional" {{ ($settings->shipping_mode ?? 'conditional') == 'conditional' ? 'selected' : '' }}>Free Delivery Above Threshold</option>
                                </select>
                            </div>

                            <div id="shipping_flat_fee_wrapper" class="collapsible-wrapper">
                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                    <span>Flat Delivery Fee</span>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">رقم</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" name="shipping_flat_fee" value="{{ $settings->shipping_flat_fee ?? 250 }}" min="0" class="input-premium-v2 pr-14">
                                    <span class="absolute right-4 text-xs text-slate-500 font-extrabold">Rs.</span>
                                </div>
                            </div>

                            <div id="shipping_threshold_wrapper" class="collapsible-wrapper">
                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                    <span>Free Delivery Threshold</span>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">حد</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="number" name="shipping_threshold" value="{{ $settings->shipping_threshold ?? 2000 }}" min="0" class="input-premium-v2 pr-14">
                                    <span class="absolute right-4 text-xs text-slate-500 font-extrabold">Rs.</span>
                                </div>
                                <p class="text-[10px] text-slate-405 mt-2.5 font-medium leading-relaxed bg-slate-50 border border-slate-100 p-3.5 rounded-2xl">
                                    📢 Orders equal to or exceeding this value will qualify for free delivery. Otherwise, the flat rate is applied.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Cash on Delivery (COD) -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center justify-between mb-8 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-md shadow-amber-500/25">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Cash on Delivery (COD)</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Collect cash upon delivery</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div class="flex items-center justify-between p-4.5 bg-slate-50/50 rounded-2xl border-2 border-slate-150 hover:border-slate-200 transition">
                                <div>
                                    <span class="text-xs font-black text-slate-900 block">Enable Cash on Delivery</span>
                                    <span class="text-[10px] text-slate-400 font-semibold mt-0.5 block">Let buyers pay cash at doorsteps</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" name="payment_cod_active" value="1" class="sr-only peer" {{ ($settings->payment_cod_active ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-orange-500 shadow-inner"></div>
                                </label>
                            </div>
                            <div class="p-4.5 bg-emerald-50/60 rounded-2xl border border-emerald-100 text-[10px] text-emerald-900 font-extrabold leading-relaxed flex gap-3 items-start shadow-sm shadow-emerald-500/5">
                                <span class="text-base leading-none">📢</span>
                                <span>COD orders trigger automated WhatsApp verification prompts on the storefront success page.</span>
                            </div>
                            <div class="pt-2 border-t border-slate-100">
                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                    <span>WhatsApp Number for Verification</span>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">واٹس ایپ نمبر</span>
                                </label>
                                <input type="text" name="footer_whatsapp" value="{{ $settings->footer_whatsapp }}" placeholder="e.g. 03001234567" class="input-premium-v2">
                                <p class="text-[10px] text-slate-400 font-semibold mt-1.5">Customers will send confirmation messages to this number. If empty, the primary phone number will be used.</p>
                            </div>

                            <!-- COD Advance Payment Option -->
                            <div class="pt-4 border-t border-slate-100">
                                <div class="flex items-center justify-between p-4.5 bg-slate-50/50 rounded-2xl border-2 border-slate-150 hover:border-slate-200 transition">
                                    <div>
                                        <span class="text-xs font-black text-slate-900 block">Require COD Advance Payment</span>
                                        <span class="text-[10px] text-slate-400 font-semibold mt-0.5 block">Require customers to pay a deposit for COD</span>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" name="cod_require_advance" id="cod_require_advance" value="1" onchange="toggleCodAdvanceFields()" class="sr-only peer" {{ ($settings->cod_require_advance ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-amber-500 peer-checked:to-orange-500 shadow-inner"></div>
                                    </label>
                                </div>

                                <div id="cod_advance_fields" class="collapsible-wrapper space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                <span>Advance Type</span>
                                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">پیمنٹ کی قسم</span>
                                            </label>
                                            <select name="cod_advance_type" class="select-premium-v2">
                                                <option value="flat" {{ ($settings->cod_advance_type ?? 'flat') === 'flat' ? 'selected' : '' }}>Flat Amount</option>
                                                <option value="percentage" {{ ($settings->cod_advance_type ?? 'flat') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                <span>Advance Value</span>
                                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">رقم / فیصد</span>
                                            </label>
                                            <input type="number" name="cod_advance_value" value="{{ $settings->cod_advance_value ?? 0 }}" min="0" step="any" class="input-premium-v2">
                                        </div>
                                    </div>
                                    <!-- Structured Advance Payment Method Selection -->
                                    <div>
                                        <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                            <span>Select Deposit Method for Advance</span>
                                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">ڈپازٹ کا طریقہ</span>
                                        </label>
                                        <select name="cod_advance_method" id="cod_advance_method" onchange="toggleCodAdvanceMethodFields()" class="select-premium-v2">
                                            <option value="bank" {{ ($settings->cod_advance_method ?? 'easypaisa') === 'bank' ? 'selected' : '' }}>Direct Bank Account Transfer</option>
                                            <option value="easypaisa" {{ ($settings->cod_advance_method ?? 'easypaisa') === 'easypaisa' ? 'selected' : '' }}>EasyPaisa Mobile Wallet</option>
                                            <option value="jazzcash" {{ ($settings->cod_advance_method ?? 'easypaisa') === 'jazzcash' ? 'selected' : '' }}>JazzCash Mobile Wallet</option>
                                        </select>
                                    </div>

                                    <!-- Bank fields for COD Advance -->
                                    <div id="cod_adv_sub_bank" class="space-y-4 pt-2 border-t border-slate-100" style="display: none;">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>Bank Name</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">بینک کا نام</span>
                                                </label>
                                                <input type="text" name="cod_advance_bank_name" value="{{ $settings->cod_advance_bank_name }}" placeholder="e.g. Meezan Bank, HBL" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>Account Title</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر نام</span>
                                                </label>
                                                <input type="text" name="cod_advance_account_title" value="{{ $settings->cod_advance_account_title }}" placeholder="e.g. John Doe" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                <span>Account Number / IBAN</span>
                                                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ نمبر / آئی بان</span>
                                            </label>
                                            <input type="text" name="cod_advance_account_number" value="{{ $settings->cod_advance_account_number }}" placeholder="e.g. PK00MEZN00..." class="input-premium-v2 cod-adv-sub-input">
                                        </div>
                                    </div>

                                    <!-- EasyPaisa fields for COD Advance -->
                                    <div id="cod_adv_sub_easypaisa" class="space-y-4 pt-2 border-t border-slate-100" style="display: none;">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>EasyPaisa Account Title</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر نام</span>
                                                </label>
                                                <input type="text" name="cod_advance_easypaisa_title" value="{{ $settings->cod_advance_easypaisa_title }}" placeholder="e.g. Muhammad Ali" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>EasyPaisa Mobile Number</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">موبائل نمبر</span>
                                                </label>
                                                <input type="text" name="cod_advance_easypaisa_number" value="{{ $settings->cod_advance_easypaisa_number }}" placeholder="e.g. 03001234567" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- JazzCash fields for COD Advance -->
                                    <div id="cod_adv_sub_jazzcash" class="space-y-4 pt-2 border-t border-slate-100" style="display: none;">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>JazzCash Account Title</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر نام</span>
                                                </label>
                                                <input type="text" name="cod_advance_jazzcash_title" value="{{ $settings->cod_advance_jazzcash_title }}" placeholder="e.g. Muhammad Ali" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2 flex items-center justify-between">
                                                    <span>JazzCash Mobile Number</span>
                                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">موبائل نمبر</span>
                                                </label>
                                                <input type="text" name="cod_advance_jazzcash_number" value="{{ $settings->cod_advance_jazzcash_number }}" placeholder="e.g. 03001234567" class="input-premium-v2 cod-adv-sub-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Electronic Transfer Channels (7 Columns) -->
                <div class="lg:col-span-7 space-y-8">
                    
                    <!-- Card 3: Bank Account Transfer -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center justify-between mb-8 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-indigo-700 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Direct Bank Account Transfer</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Accept direct bank deposits</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="payment_bank_active" id="payment_bank_active" value="1" onchange="togglePaymentFieldBlock('bank')" class="sr-only peer" {{ ($settings->payment_bank_active ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-indigo-600 peer-checked:to-indigo-800 shadow-inner"></div>
                            </label>
                        </div>

                        <div id="payment_bank_fields" class="payment-fields-block space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Bank Name</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">بینک کا نام</span>
                                    </label>
                                    <input type="text" name="payment_bank_name" value="{{ $settings->payment_bank_name }}" placeholder="e.g. Meezan Bank, HBL" class="input-premium-v2">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Account Title</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر کا نام</span>
                                    </label>
                                    <input type="text" name="payment_bank_title" value="{{ $settings->payment_bank_title }}" placeholder="e.g. John Doe" class="input-premium-v2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                    <span>Account Number / IBAN</span>
                                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ نمبر یا آئی بان</span>
                                </label>
                                <input type="text" name="payment_bank_number" value="{{ $settings->payment_bank_number }}" placeholder="e.g. PK00MEZN00..." class="input-premium-v2">
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: EasyPaisa Mobile Wallet -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center justify-between mb-8 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-400 to-teal-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/25">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">EasyPaisa Mobile Wallet</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Accept payments through EasyPaisa mobile account</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="payment_easypaisa_active" id="payment_easypaisa_active" value="1" onchange="togglePaymentFieldBlock('easypaisa')" class="sr-only peer" {{ ($settings->payment_easypaisa_active ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 shadow-inner"></div>
                            </label>
                        </div>

                        <div id="payment_easypaisa_fields" class="payment-fields-block space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Account Title</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر کا نام</span>
                                    </label>
                                    <input type="text" name="payment_easypaisa_title" value="{{ $settings->payment_easypaisa_title }}" placeholder="e.g. Muhammad Ali" class="input-premium-v2">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Mobile Account Number</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">موبائل اکاؤنٹ نمبر</span>
                                    </label>
                                    <input type="text" name="payment_easypaisa_number" value="{{ $settings->payment_easypaisa_number }}" placeholder="e.g. 03001234567" class="input-premium-v2">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: JazzCash Mobile Wallet -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center justify-between mb-8 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-500 to-amber-500 flex items-center justify-center text-white shadow-md shadow-rose-500/25">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">JazzCash Mobile Wallet</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Accept payments through JazzCash mobile account</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" name="payment_jazzcash_active" id="payment_jazzcash_active" value="1" onchange="togglePaymentFieldBlock('jazzcash')" class="sr-only peer" {{ ($settings->payment_jazzcash_active ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-500/20 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-rose-500 peer-checked:to-amber-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div id="payment_jazzcash_fields" class="payment-fields-block space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Account Title</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">اکاؤنٹ ہولڈر کا نام</span>
                                    </label>
                                    <input type="text" name="payment_jazzcash_title" value="{{ $settings->payment_jazzcash_title }}" placeholder="e.g. Muhammad Ali" class="input-premium-v2">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 tracking-wide mb-2.5 flex items-center justify-between">
                                        <span>Mobile Account Number</span>
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">موبائل اکاؤنٹ نمبر</span>
                                    </label>
                                    <input type="text" name="payment_jazzcash_number" value="{{ $settings->payment_jazzcash_number }}" placeholder="e.g. 03001234567" class="input-premium-v2">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Sticky Save Footer Bar -->
            <div class="fixed bottom-0 inset-x-0 bg-white/90 backdrop-blur-md border-t border-slate-200/60 py-4.5 px-6 flex items-center justify-end z-40 shadow-xl">
                <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
                    <div class="hidden md:flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Live Syncing Enabled</span>
                    </div>
                    <div class="flex items-center gap-3 ml-auto">
                        <a href="/shop" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs px-6 py-3.5 rounded-2xl transition duration-150 border border-slate-200">
                            Cancel
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold text-xs px-8 py-3.5 rounded-2xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/35 transition duration-200 flex items-center gap-1.5 hover:scale-[1.02] active:scale-98">
                            <span>Save Configuration</span> 💳
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <script>
        function toggleShippingFields() {
            const mode = document.getElementById('shipping_mode').value;
            const flatWrapper = document.getElementById('shipping_flat_fee_wrapper');
            const thresholdWrapper = document.getElementById('shipping_threshold_wrapper');
            
            if (mode === 'free') {
                flatWrapper.style.maxHeight = '0px';
                flatWrapper.style.opacity = '0';
                flatWrapper.style.marginTop = '0px';
                flatWrapper.style.pointerEvents = 'none';
                
                thresholdWrapper.style.maxHeight = '0px';
                thresholdWrapper.style.opacity = '0';
                thresholdWrapper.style.marginTop = '0px';
                thresholdWrapper.style.pointerEvents = 'none';
            } else if (mode === 'flat') {
                flatWrapper.style.maxHeight = '150px';
                flatWrapper.style.opacity = '1';
                flatWrapper.style.marginTop = '1.25rem';
                flatWrapper.style.pointerEvents = 'auto';
                
                thresholdWrapper.style.maxHeight = '0px';
                thresholdWrapper.style.opacity = '0';
                thresholdWrapper.style.marginTop = '0px';
                thresholdWrapper.style.pointerEvents = 'none';
            } else if (mode === 'conditional') {
                flatWrapper.style.maxHeight = '150px';
                flatWrapper.style.opacity = '1';
                flatWrapper.style.marginTop = '1.25rem';
                flatWrapper.style.pointerEvents = 'auto';
                
                thresholdWrapper.style.maxHeight = '350px';
                thresholdWrapper.style.opacity = '1';
                thresholdWrapper.style.marginTop = '1.25rem';
                thresholdWrapper.style.pointerEvents = 'auto';
            }
        }
        
        function togglePaymentFieldBlock(type) {
            const checkbox = document.getElementById('payment_' + type + '_active');
            const block = document.getElementById('payment_' + type + '_fields');
            if (checkbox && block) {
                if (checkbox.checked) {
                    block.classList.add('active');
                    block.querySelectorAll('input').forEach(i => i.removeAttribute('disabled'));
                } else {
                    block.classList.remove('active');
                    block.querySelectorAll('input').forEach(i => i.setAttribute('disabled', 'true'));
                }
            }
        }

        function toggleCodAdvanceMethodFields() {
            const methodSelect = document.getElementById('cod_advance_method');
            if (!methodSelect) return;
            
            const selectedMethod = methodSelect.value;
            const methods = ['bank', 'easypaisa', 'jazzcash'];
            const requireCheckbox = document.getElementById('cod_require_advance');
            const codRequireAdvance = requireCheckbox ? requireCheckbox.checked : false;
            
            methods.forEach(m => {
                const block = document.getElementById('cod_adv_sub_' + m);
                if (block) {
                    if (m === selectedMethod) {
                        block.style.display = 'block';
                        block.querySelectorAll('.cod-adv-sub-input').forEach(i => {
                            if (codRequireAdvance) {
                                i.removeAttribute('disabled');
                            } else {
                                i.setAttribute('disabled', 'true');
                            }
                        });
                    } else {
                        block.style.display = 'none';
                        block.querySelectorAll('.cod-adv-sub-input').forEach(i => i.setAttribute('disabled', 'true'));
                    }
                }
            });
        }

        function toggleCodAdvanceFields() {
            const requireCheckbox = document.getElementById('cod_require_advance');
            const block = document.getElementById('cod_advance_fields');
            if (requireCheckbox && block) {
                if (requireCheckbox.checked) {
                    block.classList.add('active');
                    block.style.maxHeight = '1000px';
                    block.style.opacity = '1';
                    block.style.marginTop = '1.25rem';
                    block.style.pointerEvents = 'auto';
                    block.querySelectorAll('input, select, textarea').forEach(i => {
                        if (!i.classList.contains('cod-adv-sub-input')) {
                            i.removeAttribute('disabled');
                        }
                    });
                    toggleCodAdvanceMethodFields();
                } else {
                    block.classList.remove('active');
                    block.style.maxHeight = '0px';
                    block.style.opacity = '0';
                    block.style.marginTop = '0px';
                    block.style.pointerEvents = 'none';
                    block.querySelectorAll('input, select, textarea').forEach(i => i.setAttribute('disabled', 'true'));
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleShippingFields();
            ['bank', 'easypaisa', 'jazzcash'].forEach(type => {
                togglePaymentFieldBlock(type);
            });
            toggleCodAdvanceFields();
        });
    </script>
</body>
</html>
