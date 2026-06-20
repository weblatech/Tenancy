@extends('super-admin.layout')

@section('title', 'Provision New Store')
@section('page_title', 'Manual Store Provisioning')

@section('content')
<style>
    .provision-card {
        box-shadow:
            0 0 0 1px rgba(99, 102, 241, 0.04),
            0 1px 2px rgba(0, 0, 0, 0.02),
            0 8px 24px -4px rgba(99, 102, 241, 0.06),
            0 24px 48px -8px rgba(0, 0, 0, 0.03);
    }
    .provision-card:hover {
        box-shadow:
            0 0 0 1px rgba(99, 102, 241, 0.06),
            0 1px 2px rgba(0, 0, 0, 0.02),
            0 12px 32px -4px rgba(99, 102, 241, 0.08),
            0 32px 64px -8px rgba(0, 0, 0, 0.04);
    }
    .input-premium {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .input-premium:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .section-badge {
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    }
    .btn-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%);
        background-size: 200% 200%;
        animation: gradientShift 4s ease infinite;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px -4px rgba(99, 102, 241, 0.45), 0 4px 12px -2px rgba(124, 58, 237, 0.2);
    }
    .btn-submit:active {
        transform: translateY(0);
    }
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .error-card {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
    }
    .section-divider {
        background: linear-gradient(90deg, transparent 0%, #e2e8f0 20%, #e2e8f0 80%, transparent 100%);
        height: 1px;
    }
    .fade-in {
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Back Navigation --}}
    <div class="fade-in" style="animation-delay: 0.05s">
        <a href="/admin/tenants" class="group inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wider text-indigo-600 bg-indigo-50/80 hover:bg-indigo-100 border border-indigo-100 hover:border-indigo-200 transition-all duration-200 px-4 py-2.5 rounded-full">
            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            Back to Store List
        </a>
    </div>

    {{-- Main Provisioning Card --}}
    <div class="bg-white rounded-3xl provision-card transition-shadow duration-500 fade-in" style="animation-delay: 0.1s">

        {{-- Card Header --}}
        <div class="px-8 pt-8 pb-6">
            <div class="flex items-start gap-4">
                <div class="bg-gradient-to-br from-indigo-500 to-violet-600 p-3 rounded-2xl shadow-lg shadow-indigo-500/20 shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 tracking-tight">Manual Store Provisioning</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-1 leading-relaxed max-w-md">
                        Configure store parameters, subscription tier, and merchant credentials to spin up a new isolated storefront instance.
                    </p>
                </div>
            </div>
        </div>

        <div class="section-divider mx-8"></div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mx-8 mt-6">
                <div class="error-card border border-rose-200/80 px-5 py-4 rounded-2xl">
                    <div class="flex items-start gap-3">
                        <div class="bg-rose-500 p-1.5 rounded-lg shadow-sm shadow-rose-500/20 shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-extrabold text-rose-900 uppercase tracking-wide">Validation Failed</p>
                            <ul class="mt-1.5 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li class="text-xs font-semibold text-rose-700 flex items-center gap-1.5">
                                        <span class="w-1 h-1 rounded-full bg-rose-400 shrink-0"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="/admin/tenants/create" method="POST" class="px-8 pb-8 pt-6 space-y-8" id="provisionForm">
            @csrf

            {{-- ─── Section 1: Store Details ─── --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <span class="section-badge text-indigo-700 font-black text-[10px] w-6 h-6 rounded-lg flex items-center justify-center border border-indigo-200/60 shadow-sm">1</span>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Store Details</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Store ID --}}
                    <div>
                        <label for="store_id" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
                            </svg>
                            Store ID / Slug
                        </label>
                        <input type="text" name="store_id" id="store_id" value="{{ old('store_id') }}" placeholder="e.g. fashionhub" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-300 placeholder:font-medium">
                        <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Alpha-numeric characters and hyphens only</p>
                    </div>

                    {{-- Store Name --}}
                    <div>
                        <label for="store_name" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72"/>
                            </svg>
                            Store Name
                        </label>
                        <input type="text" name="store_name" id="store_name" value="{{ old('store_name') }}" placeholder="e.g. Fashion Hub Store" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-300 placeholder:font-medium">
                    </div>
                </div>

                {{-- Domain --}}
                <div>
                    <label for="domain" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>
                        </svg>
                        Store Domain (URL)
                    </label>
                    <div class="relative">
                        <input type="text" name="domain" id="domain" value="{{ old('domain') }}" placeholder="e.g. fashionhub.store" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-300 placeholder:font-medium pr-10">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium leading-relaxed flex items-center gap-1">
                        <svg class="w-3 h-3 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        Local development defaults to <strong class="text-slate-500">slug.store</strong> — auto-filled as you type the Store ID.
                    </p>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ─── Section 2: Subscription Settings ─── --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <span class="section-badge text-indigo-700 font-black text-[10px] w-6 h-6 rounded-lg flex items-center justify-center border border-indigo-200/60 shadow-sm">2</span>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Subscription Settings</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {{-- Plan --}}
                    <div>
                        <label for="subscription_plan" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                            </svg>
                            Subscription Plan
                        </label>
                        <select name="subscription_plan" id="subscription_plan" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 appearance-none cursor-pointer"
                            style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%2394a3b8%22><path fill-rule=%22evenodd%22 d=%22M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z%22 clip-rule=%22evenodd%22/></svg>'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px;">
                            <option value="free" {{ old('subscription_plan') === 'free' ? 'selected' : '' }}>Free Trial</option>
                            <option value="basic" {{ old('subscription_plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ old('subscription_plan', 'pro') === 'pro' ? 'selected' : '' }}>Pro (Recommended)</option>
                            <option value="enterprise" {{ old('subscription_plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="subscription_status" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Initial Status
                        </label>
                        <select name="subscription_status" id="subscription_status" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 appearance-none cursor-pointer"
                            style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22%2394a3b8%22><path fill-rule=%22evenodd%22 d=%22M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z%22 clip-rule=%22evenodd%22/></svg>'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px;">
                            <option value="active" {{ old('subscription_status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ old('subscription_status') === 'trial' ? 'selected' : '' }}>Trial Mode</option>
                            <option value="expired" {{ old('subscription_status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="suspended" {{ old('subscription_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    {{-- Expiration --}}
                    <div>
                        <label for="subscription_ends_at" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                            Expiration Date
                        </label>
                        <input type="date" name="subscription_ends_at" id="subscription_ends_at" value="{{ old('subscription_ends_at') }}"
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10">
                        <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Optional — leave blank for no expiry</p>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- ─── Section 3: Merchant Account ─── --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3">
                    <span class="section-badge text-indigo-700 font-black text-[10px] w-6 h-6 rounded-lg flex items-center justify-center border border-indigo-200/60 shadow-sm">3</span>
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Merchant Account</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Merchant Name --}}
                    <div>
                        <label for="merchant_name" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            Merchant Name
                        </label>
                        <input type="text" name="merchant_name" id="merchant_name" value="{{ old('merchant_name') }}" placeholder="e.g. John Doe" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-300 placeholder:font-medium">
                    </div>

                    {{-- Merchant Email --}}
                    <div>
                        <label for="merchant_email" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            Merchant Email
                        </label>
                        <input type="email" name="merchant_email" id="merchant_email" value="{{ old('merchant_email') }}" placeholder="e.g. merchant@example.com" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 placeholder:text-slate-300 placeholder:font-medium">
                    </div>
                </div>

                {{-- Merchant Password --}}
                <div>
                    <label for="merchant_password" class="flex items-center gap-1.5 text-[11px] font-extrabold text-slate-600 tracking-wide mb-2.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                        </svg>
                        Temporary Password
                    </label>
                    <div class="relative">
                        <input type="text" name="merchant_password" id="merchant_password" value="{{ old('merchant_password', Str::random(10)) }}" required
                            class="input-premium w-full px-4 py-3 text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 font-mono tracking-wider pr-24">
                        <button type="button" id="regeneratePassword"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-[10px] font-extrabold px-3 py-1.5 rounded-lg transition-all duration-200 uppercase tracking-wider border border-indigo-100 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                            </svg>
                            Regen
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium leading-relaxed flex items-center gap-1">
                        <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        Auto-generated — share this with the merchant for initial dashboard access.
                    </p>
                </div>
            </div>

            {{-- ─── Form Actions ─── --}}
            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                <a href="/admin/tenants"
                    class="group inline-flex items-center gap-2 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-800 font-extrabold text-xs px-6 py-3 rounded-xl transition-all duration-200 border border-slate-200 hover:border-slate-300">
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                    class="btn-submit inline-flex items-center gap-2 text-white font-black text-xs px-8 py-3.5 rounded-xl shadow-lg shadow-indigo-500/20">
                    <span>Spin Up Store</span>
                    <span class="text-sm">🚀</span>
                </button>
            </div>

        </form>
    </div>

</div>

<script>
    // ─── UX Helpers ───
    const storeIdInput = document.getElementById('store_id');
    const storeNameInput = document.getElementById('store_name');
    const domainInput = document.getElementById('domain');
    const regenBtn = document.getElementById('regeneratePassword');
    const passwordInput = document.getElementById('merchant_password');

    // Auto-populate domain + suggest store name as user types store ID
    storeIdInput.addEventListener('input', (e) => {
        const slug = e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        e.target.value = slug;

        // Auto-fill domain
        domainInput.value = slug ? `${slug}.store` : '';

        // Auto-suggest store name (only if empty or still contains default pattern)
        if (slug && (!storeNameInput.value || storeNameInput.dataset.autoFilled === 'true')) {
            const formatted = slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            storeNameInput.value = formatted + ' Store';
            storeNameInput.dataset.autoFilled = 'true';
        }
    });

    // Clear auto-fill flag when user manually edits store name
    storeNameInput.addEventListener('input', () => {
        storeNameInput.dataset.autoFilled = 'false';
    });

    // Regenerate password button
    regenBtn.addEventListener('click', () => {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let pass = '';
        for (let i = 0; i < 10; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        passwordInput.value = pass;

        // Visual feedback
        regenBtn.classList.add('scale-95');
        setTimeout(() => regenBtn.classList.remove('scale-95'), 150);
    });
</script>
@endsection
