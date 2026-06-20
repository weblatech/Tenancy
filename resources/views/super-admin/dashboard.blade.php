@extends('super-admin.layout')

@section('title', 'Super Admin Console')
@section('page_title', 'Platform Analytics')

@section('content')
<style>
    .stat-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 1.25rem;
        padding: 1.75rem;
        box-shadow:
            0 10px 40px -10px rgba(99, 102, 241, 0.07),
            0 4px 12px -2px rgba(0, 0, 0, 0.03);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow:
            0 20px 50px -12px rgba(99, 102, 241, 0.15),
            0 8px 24px -4px rgba(0, 0, 0, 0.06);
    }
    .stat-card:hover::before {
        opacity: 1;
    }
    .stat-card.card-indigo::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
    .stat-card.card-emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.card-rose::before { background: linear-gradient(90deg, #f43f5e, #fb7185); }
    .stat-card.card-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .glass-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 1.25rem;
        box-shadow:
            0 10px 40px -10px rgba(99, 102, 241, 0.06),
            0 4px 12px -2px rgba(0, 0, 0, 0.02);
    }

    .icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .icon-circle svg {
        width: 24px;
        height: 24px;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a78bfa 100%);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .spark-bar {
        height: 6px;
        border-radius: 3px;
        background: #f1f5f9;
        overflow: hidden;
        position: relative;
    }
    .spark-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }
    .table-row-hover:hover {
        background-color: rgba(248, 250, 252, 0.8);
    }

    .manage-btn {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
    }
    .manage-btn:hover {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border-color: #c7d2fe;
        color: #4f46e5;
    }

    .delete-btn {
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.25s ease;
        border-radius: 8px;
        padding: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .delete-btn:hover {
        background: #fff1f2;
        border-color: #fecdd3;
    }
    .delete-btn svg {
        transition: color 0.25s ease;
    }
    .delete-btn:hover svg {
        color: #e11d48;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-in {
        animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    .animate-delay-1 { animation-delay: 0.05s; }
    .animate-delay-2 { animation-delay: 0.1s; }
    .animate-delay-3 { animation-delay: 0.15s; }
    .animate-delay-4 { animation-delay: 0.2s; }
    .animate-delay-5 { animation-delay: 0.3s; }
    .animate-delay-6 { animation-delay: 0.4s; }
</style>

<div class="space-y-8">

    {{-- ═══════════════ WELCOME BANNER ═══════════════ --}}
    <div class="welcome-banner px-8 py-7 animate-in">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-white text-xl font-extrabold tracking-tight">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}</h2>
                <p class="text-indigo-200 text-sm font-semibold mt-1.5">Here's what's happening across your platform today.</p>
            </div>
            <div class="hidden md:flex items-center gap-3 bg-white/10 backdrop-blur-sm rounded-2xl px-5 py-3 border border-white/10">
                <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-white text-sm font-bold">{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════ STAT CARDS GRID ═══════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Total Stores --}}
        <div class="stat-card card-indigo animate-in animate-delay-1">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] block">Total Stores</span>
                    <span class="text-4xl font-black text-slate-900 block tracking-tight">{{ $totalStores }}</span>
                    <span class="text-[11px] font-bold text-slate-400 block">Onboarded on platform</span>
                </div>
                <div class="icon-circle" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
                    <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Stores --}}
        <div class="stat-card card-emerald animate-in animate-delay-2">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] block">Active Stores</span>
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl font-black text-slate-900 tracking-tight">{{ $activeStores }}</span>
                        <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                            {{ $totalStores > 0 ? round(($activeStores / $totalStores) * 100) : 0 }}%
                        </span>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-600 block">Currently operational</span>
                </div>
                <div class="icon-circle" style="background: linear-gradient(135deg, #10b981, #34d399);">
                    <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Expired Stores --}}
        <div class="stat-card card-rose animate-in animate-delay-3">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] block">Expired Stores</span>
                    <span class="text-4xl font-black text-slate-900 block tracking-tight">{{ $expiredStores }}</span>
                    <span class="text-[11px] font-bold text-rose-500 block">Subscription lapsed</span>
                </div>
                <div class="icon-circle" style="background: linear-gradient(135deg, #f43f5e, #fb7185);">
                    <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Suspended Stores --}}
        <div class="stat-card card-amber animate-in animate-delay-4">
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] block">Suspended Stores</span>
                    <span class="text-4xl font-black text-slate-900 block tracking-tight">{{ $suspendedStores }}</span>
                    <span class="text-[11px] font-bold text-amber-500 block">Pending review</span>
                </div>
                <div class="icon-circle" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                    <svg fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════ BOTTOM 2-COLUMN LAYOUT ═══════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ────── LEFT: Platform Overview ────── --}}
        <div class="lg:col-span-1 glass-card p-7 space-y-6 animate-in animate-delay-5">
            <div>
                <h3 class="text-sm font-black text-slate-900 tracking-wide">Platform Overview</h3>
                <p class="text-[11px] text-slate-400 font-semibold mt-1">Aggregated metrics across all stores</p>
            </div>

            <div class="space-y-5">
                {{-- Merchants --}}
                <div class="p-4 bg-gradient-to-r from-slate-50 to-white rounded-2xl border border-slate-100/80">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
                                <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-700 block">Total Merchants</span>
                                <span class="text-[10px] text-slate-400 font-semibold block">Registered users</span>
                            </div>
                        </div>
                        <span class="text-xl font-black text-slate-900">{{ number_format($totalMerchants) }}</span>
                    </div>
                    <div class="spark-bar">
                        <div class="spark-bar-fill" style="width: {{ $totalMerchants > 0 ? min(100, max(15, ($totalMerchants / max($totalMerchants, $totalProducts, $totalOrders, 1)) * 100)) : 8 }}%; background: linear-gradient(90deg, #6366f1, #818cf8);"></div>
                    </div>
                </div>

                {{-- Products --}}
                <div class="p-4 bg-gradient-to-r from-slate-50 to-white rounded-2xl border border-slate-100/80">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                                <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-700 block">Total Products</span>
                                <span class="text-[10px] text-slate-400 font-semibold block">Catalog items</span>
                            </div>
                        </div>
                        <span class="text-xl font-black text-slate-900">{{ number_format($totalProducts) }}</span>
                    </div>
                    <div class="spark-bar">
                        <div class="spark-bar-fill" style="width: {{ $totalProducts > 0 ? min(100, max(15, ($totalProducts / max($totalMerchants, $totalProducts, $totalOrders, 1)) * 100)) : 8 }}%; background: linear-gradient(90deg, #8b5cf6, #a78bfa);"></div>
                    </div>
                </div>

                {{-- Orders --}}
                <div class="p-4 bg-gradient-to-r from-slate-50 to-white rounded-2xl border border-slate-100/80">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                                <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-700 block">Total Orders</span>
                                <span class="text-[10px] text-slate-400 font-semibold block">Lifetime transactions</span>
                            </div>
                        </div>
                        <span class="text-xl font-black text-slate-900">{{ number_format($totalOrders) }}</span>
                    </div>
                    <div class="spark-bar">
                        <div class="spark-bar-fill" style="width: {{ $totalOrders > 0 ? min(100, max(15, ($totalOrders / max($totalMerchants, $totalProducts, $totalOrders, 1)) * 100)) : 8 }}%; background: linear-gradient(90deg, #ec4899, #f472b6);"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────── RIGHT: Recent Stores Table ────── --}}
        <div class="lg:col-span-2 glass-card p-7 space-y-6 animate-in animate-delay-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 tracking-wide">Recent Stores</h3>
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">Latest onboarded tenants</p>
                </div>
                <a href="/admin/tenants" class="flex items-center gap-1.5 text-xs font-extrabold text-indigo-600 hover:text-indigo-700 transition group">
                    <span>View All</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto -mx-1">
                <table class="w-full text-left border-collapse min-w-[700px]">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em] pl-1">Store ID</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em]">Store Name</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em]">Domain</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em]">Plan</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em]">Status</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em]">Expiry Date</th>
                            <th class="pb-3.5 text-[10px] font-black text-slate-400 uppercase tracking-[0.08em] text-right pr-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentStores as $store)
                            <tr class="table-row-hover">
                                {{-- Store ID --}}
                                <td class="py-4 pl-1">
                                    <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">#{{ $store->id }}</span>
                                </td>

                                {{-- Store Name --}}
                                <td class="py-4">
                                    <span class="text-xs font-extrabold text-slate-900">{{ $store->name ?? 'Unnamed Store' }}</span>
                                </td>

                                {{-- Domain --}}
                                <td class="py-4">
                                    @if($store->domains->isNotEmpty())
                                        <a href="http://{{ $store->domains->first()->domain }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 hover:underline underline-offset-2 transition">
                                            {{ $store->domains->first()->domain }}
                                        </a>
                                    @else
                                        <span class="text-xs font-semibold text-slate-300 italic">No domain</span>
                                    @endif
                                </td>

                                {{-- Plan Badge --}}
                                <td class="py-4">
                                    @php
                                        $plan = strtolower($store->subscription_plan ?? 'free');
                                        $planStyles = [
                                            'pro' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60',
                                            'enterprise' => 'bg-violet-50 text-violet-700 border-violet-200/60',
                                            'basic' => 'bg-sky-50 text-sky-700 border-sky-200/60',
                                            'free' => 'bg-slate-50 text-slate-500 border-slate-200/60',
                                        ];
                                        $style = $planStyles[$plan] ?? $planStyles['free'];
                                    @endphp
                                    <span class="text-[9px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-lg border {{ $style }}">
                                        {{ $store->subscription_plan ?? 'Free' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="py-4">
                                    @php
                                        $status = strtolower($store->subscription_status ?? 'active');
                                        $statusMap = [
                                            'active'    => ['color' => 'text-emerald-600', 'dot' => 'bg-emerald-500'],
                                            'trial'     => ['color' => 'text-sky-600',     'dot' => 'bg-sky-500'],
                                            'suspended' => ['color' => 'text-amber-600',   'dot' => 'bg-amber-500'],
                                            'expired'   => ['color' => 'text-rose-600',    'dot' => 'bg-rose-500'],
                                        ];
                                        $st = $statusMap[$status] ?? $statusMap['active'];
                                    @endphp
                                    <span class="flex items-center gap-2 {{ $st['color'] }}">
                                        <span class="w-2 h-2 rounded-full {{ $st['dot'] }} ring-2 ring-offset-1 {{ str_replace('bg-', 'ring-', $st['dot']) }}/20"></span>
                                        <span class="text-xs font-bold">{{ ucfirst($status) }}</span>
                                    </span>
                                </td>

                                {{-- Expiry Date --}}
                                <td class="py-4">
                                    @if($store->subscription_ends_at)
                                        <span class="text-xs font-semibold text-slate-500">
                                            {{ \Carbon\Carbon::parse($store->subscription_ends_at)->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-slate-300 italic">N/A</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="py-4 text-right pr-1">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/admin/tenants/{{ $store->id }}" class="manage-btn text-slate-700 font-extrabold text-[10px] px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Manage
                                        </a>
                                        <form action="/admin/tenants/{{ $store->id }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this store? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn" title="Delete Store">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-bold text-slate-400">No stores registered yet</span>
                                        <a href="/admin/tenants/create" class="text-xs font-extrabold text-indigo-600 hover:text-indigo-700 transition">
                                            Create your first store
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
