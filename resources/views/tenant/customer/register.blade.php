<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->enable_rtl ? 'رجسٹریشن' : 'Customer Register' }} - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    @if($settings->enable_rtl)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/pakeezah-fonts/jameel-noori-nastaleeq@1.0.0/index.css">
    <style>
        html { font-size: 115% !important; }
        body { direction: rtl; text-align: right; font-family: 'Jameel Noori Nastaleeq', sans-serif !important; }
        p, span, h1, h2, h3, h4, h5, h6, a, button, input, textarea { line-height: 1.6 !important; }
    </style>
    @endif

    <style>
        .btn-primary-custom {
            background-color: {{ $settings->btn_primary_bg ?? '#16a34a' }} !important;
            color: {{ $settings->btn_primary_text ?? '#ffffff' }} !important;
        }
        .btn-primary-custom:hover { opacity: 0.95; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <a href="{{ tenant_store_url('/') }}" class="text-3xl font-black text-slate-800 tracking-tight flex items-center justify-center gap-1.5">
            🛍️ {{ strtoupper($tenantId) }}
        </a>
        <h2 class="mt-6 text-2xl font-black text-slate-800 leading-tight">
            {{ $settings->enable_rtl ? 'نیا کسٹمر اکاؤنٹ بنائیں' : 'Create a new customer account' }}
        </h2>
        <p class="mt-2 text-xs font-semibold text-slate-450">
            {{ $settings->enable_rtl ? 'پہلے سے اکاؤنٹ موجود ہے؟' : 'Already have an account?' }}
            <a href="/customer/login" class="text-indigo-600 hover:text-indigo-500 font-extrabold hover:underline">
                {{ $settings->enable_rtl ? 'لاگ ان کریں' : 'Log in here' }}
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl border border-slate-100 sm:rounded-3xl sm:px-10">

            <form action="/customer/register" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                        {{ $settings->enable_rtl ? 'آپ کا نام' : 'Your Name' }} <span class="text-red-500">*</span>
                    </label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        placeholder="{{ $settings->enable_rtl ? 'مثال: علی خان' : 'e.g. Ali Khan' }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                        {{ $settings->enable_rtl ? 'ای میل ایڈریس' : 'Email Address' }} <span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                        placeholder="ali@example.com"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                            {{ $settings->enable_rtl ? 'فون نمبر' : 'Phone Number' }}
                        </label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                            placeholder="03001234567"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                        @error('phone')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                            {{ $settings->enable_rtl ? 'شہر' : 'City' }}
                        </label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}"
                            placeholder="Lahore"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                        @error('city')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                        {{ $settings->enable_rtl ? 'مکمل پتہ (شپنگ ایڈریس)' : 'Complete Shipping Address' }}
                    </label>
                    <textarea id="address" name="address" rows="3"
                        placeholder="{{ $settings->enable_rtl ? 'گھر نمبر، گلی اور محلہ' : 'House/Street/Area details' }}"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150 resize-none">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                            {{ $settings->enable_rtl ? 'پاس ورڈ' : 'Password' }} <span class="text-red-500">*</span>
                        </label>
                        <input id="password" name="password" type="password" required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                        @error('password')
                            <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1.5">
                            {{ $settings->enable_rtl ? 'پاس ورڈ کنفرم کریں' : 'Confirm Password' }} <span class="text-red-500">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary-custom w-full font-black py-4 px-6 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                        <span>{{ $settings->enable_rtl ? 'رجسٹر کریں' : 'Register' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ tenant_store_url('/') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition flex items-center justify-center gap-1">
                    ← {{ $settings->enable_rtl ? 'سٹور پر واپس جائیں' : 'Back to Store' }}
                </a>
            </div>

        </div>
    </div>

</body>
</html>
