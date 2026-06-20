@extends('super-admin.layout')

@section('title', 'Manage Tenant Stores')
@section('page_title', 'SaaS Onboarded Stores')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Form -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/60 card-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Search & Filter Form -->
        <form action="/admin/tenants" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Search Text -->
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Store ID or Name..." 
                    class="w-full px-4 py-2 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
            </div>

            <!-- Plan Filter -->
            <select name="plan" class="px-4 py-2 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
                <option value="">All Plans</option>
                <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
            </select>

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <!-- Submit & Reset -->
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl transition">
                Apply Filters
            </button>
            @if(request()->anyFilled(['search', 'plan', 'status']))
                <a href="/admin/tenants" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>

        <!-- Create Tenant Button -->
        <a href="/admin/tenants/create" class="bg-gradient-to-r from-indigo-600 to-violet-650 hover:from-indigo-750 hover:to-violet-750 text-white font-black text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-indigo-500/10 flex items-center gap-1.5 shrink-0 self-start md:self-auto">
            <span>+ Add New Store</span>
        </a>

    </div>

    <!-- Stores Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/60 card-shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-150 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Store ID</th>
                        <th class="px-6 py-4">Store Name</th>
                        <th class="px-6 py-4">Domain</th>
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Ends At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-slate-50/35 transition">
                            <td class="px-6 py-4.5 font-bold text-slate-800">{{ $tenant->id }}</td>
                            <td class="px-6 py-4.5 font-bold text-slate-900">{{ $tenant->name ?? 'Munaa Store' }}</td>
                            <td class="px-6 py-4.5 font-semibold text-indigo-600/80">
                                @if($tenant->domains->first())
                                    <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="hover:underline">
                                        {{ $tenant->domains->first()->domain }} ↗
                                    </a>
                                @else
                                    <span class="text-slate-400">No Domain</span>
                                @endif
                            </td>
                            <td class="px-6 py-4.5">
                                <span class="font-extrabold uppercase tracking-wide text-[9px] px-2 py-0.5 rounded-md
                                    @if($tenant->subscription_plan === 'pro') bg-indigo-50 text-indigo-600 border border-indigo-150
                                    @elseif($tenant->subscription_plan === 'enterprise') bg-violet-50 text-violet-600 border border-violet-150
                                    @elseif($tenant->subscription_plan === 'basic') bg-sky-50 text-sky-600 border border-sky-150
                                    @else bg-slate-50 text-slate-500 border border-slate-150
                                    @endif">
                                    {{ $tenant->subscription_plan ?? 'Free' }}
                                </span>
                            </td>
                            <td class="px-6 py-4.5">
                                <span class="font-bold text-[10px] flex items-center gap-1.5
                                    @if($tenant->subscription_status === 'active') text-emerald-600
                                    @elseif($tenant->subscription_status === 'trial') text-sky-600
                                    @elseif($tenant->subscription_status === 'suspended') text-amber-600
                                    @else text-rose-600
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        @if($tenant->subscription_status === 'active') bg-emerald-500
                                        @elseif($tenant->subscription_status === 'trial') bg-sky-500
                                        @elseif($tenant->subscription_status === 'suspended') bg-amber-500
                                        @else bg-rose-500
                                        @endif"></span>
                                    <span>{{ ucfirst($tenant->subscription_status ?? 'Active') }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4.5 font-bold text-slate-500">
                                {{ $tenant->subscription_ends_at ? \Carbon\Carbon::parse($tenant->subscription_ends_at)->format('Y-m-d') : 'Lifetime' }}
                            </td>
                            <td class="px-6 py-4.5 text-right">
                                <a href="/admin/tenants/{{ $tenant->id }}" class="bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-extrabold text-[10px] px-3 py-2 rounded-xl transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400 font-bold">
                                No stores found matching the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                {{ $tenants->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
