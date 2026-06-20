@extends('super-admin.layout')

@section('title', 'Manage ' . ($tenant->name ?? $tenant->id))
@section('page_title', 'Store Management Console')

@section('content')
<div class="space-y-8">

    <!-- Header navigation -->
    <div class="flex items-center justify-between">
        <a href="/admin/tenants" class="text-xs font-black uppercase text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition px-3.5 py-2 rounded-full flex items-center gap-1.5 w-max">
            ← Back to Store List
        </a>

        <!-- Suspend/Activate Fast Action -->
        <form action="/admin/tenants/{{ $tenant->id }}/toggle-status" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black transition shadow-sm
                @if($tenant->subscription_status === 'suspended')
                    bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-500/10
                @else
                    bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/10
                @endif">
                @if($tenant->subscription_status === 'suspended')
                    🟢 Activate Store
                @else
                    🛑 Suspend Store Access
                @endif
            </button>
        </form>
    </div>

    <!-- Core Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Details & Analytics (7 Columns) -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Store Profile -->
            <div class="bg-white p-6.5 rounded-3xl border border-slate-200/60 card-shadow space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest block bg-indigo-50 border border-indigo-100/50 w-max px-2.5 py-0.5 rounded-md mb-1.5">Store Info</span>
                        <h3 class="text-lg font-black text-slate-900 leading-normal">{{ $tenant->name ?? 'Munaa Merchant Store' }}</h3>
                        <p class="text-[10px] text-slate-400 font-semibold">Store Slug ID: <span class="font-mono text-slate-600 font-bold bg-slate-50 px-1 py-0.5 rounded">{{ $tenant->id }}</span></p>
                    </div>
                    <div>
                        <span class="font-extrabold uppercase tracking-wide text-[9px] px-2.5 py-1 rounded-md border
                            @if($tenant->subscription_plan === 'pro') bg-indigo-50 text-indigo-600 border-indigo-150
                            @elseif($tenant->subscription_plan === 'enterprise') bg-violet-50 text-violet-600 border-violet-150
                            @elseif($tenant->subscription_plan === 'basic') bg-sky-50 text-sky-600 border-sky-150
                            @else bg-slate-50 text-slate-500 border-slate-150
                            @endif">
                            {{ $tenant->subscription_plan ?? 'Free' }} Plan
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs leading-relaxed">
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Domain Link</span>
                        @if($tenant->domains->first())
                            <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="text-indigo-650 hover:underline font-extrabold flex items-center gap-1 mt-1">
                                <span>{{ $tenant->domains->first()->domain }}</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        @else
                            <span class="text-slate-500 font-bold block mt-1">No domain associated</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Created Date</span>
                        <span class="text-slate-800 font-bold block mt-1">
                            {{ $tenant->created_at ? \Carbon\Carbon::parse($tenant->created_at)->format('Y-m-d H:i') : 'Unknown' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Subscription Status</span>
                        <span class="font-bold text-[10px] flex items-center gap-1.5 mt-1
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
                    </div>
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">End Expiration Date</span>
                        <span class="text-slate-800 font-bold block mt-1">
                            {{ $tenant->subscription_ends_at ? \Carbon\Carbon::parse($tenant->subscription_ends_at)->format('Y-m-d') : 'Lifetime / Unlimited' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Store Catalog Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Products -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 card-shadow flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Products Created</span>
                        <span class="text-2xl font-black text-slate-900 block mt-1">{{ $productCount }}</span>
                    </div>
                    <span class="text-2xl bg-indigo-50 text-indigo-650 p-2.5 rounded-2xl shadow-inner border border-indigo-100/30">📦</span>
                </div>
                <!-- Orders -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 card-shadow flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block">Orders Placed</span>
                        <span class="text-2xl font-black text-slate-900 block mt-1">{{ $orderCount }}</span>
                    </div>
                    <span class="text-2xl bg-indigo-50 text-indigo-650 p-2.5 rounded-2xl shadow-inner border border-indigo-100/30">🛍️</span>
                </div>
            </div>

            <!-- Merchant Users List -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/60 card-shadow space-y-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Merchant Users</h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Users linked to this tenant store</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="pb-2">Name</th>
                                <th class="pb-2">Email</th>
                                <th class="pb-2">Joined Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-xs">
                            @forelse($merchantUsers as $user)
                                <tr>
                                    <td class="py-2.5 font-bold text-slate-800">{{ $user->name }}</td>
                                    <td class="py-2.5 font-semibold text-slate-600">{{ $user->email }}</td>
                                    <td class="py-2.5 text-slate-400 font-semibold">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-slate-400 font-bold">
                                        No users registered under this store.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right: Actions & Subscription settings (5 Columns) -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- Subscription Manager Form -->
            <div class="bg-white p-6.5 rounded-3xl border border-slate-200/60 card-shadow space-y-6">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Manage Subscription</h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Modify plan variables or expiration end dates.</p>
                </div>

                <form action="/admin/tenants/{{ $tenant->id }}/update-subscription" method="POST" class="space-y-4.5">
                    @csrf
                    
                    <!-- Plan -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 tracking-wide mb-2">Subscription Plan</label>
                        <select name="subscription_plan" required class="w-full px-4 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
                            <option value="free" {{ $tenant->subscription_plan === 'free' ? 'selected' : '' }}>Free Trial</option>
                            <option value="basic" {{ $tenant->subscription_plan === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ $tenant->subscription_plan === 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="enterprise" {{ $tenant->subscription_plan === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 tracking-wide mb-2">Subscription Status</label>
                        <select name="subscription_status" required class="w-full px-4 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
                            <option value="active" {{ $tenant->subscription_status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="trial" {{ $tenant->subscription_status === 'trial' ? 'selected' : '' }}>Trial Mode</option>
                            <option value="expired" {{ $tenant->subscription_status === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="suspended" {{ $tenant->subscription_status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <!-- Ends At -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 tracking-wide mb-2">Ends At Expiration</label>
                        <input type="date" name="subscription_ends_at" 
                            value="{{ $tenant->subscription_ends_at ? \Carbon\Carbon::parse($tenant->subscription_ends_at)->format('Y-m-d') : '' }}"
                            class="w-full px-4 py-2.5 text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-indigo-500 focus:bg-white transition">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs py-3 rounded-xl transition shadow-md shadow-indigo-500/10">
                        Update Subscription 💳
                    </button>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white p-6.5 rounded-3xl border border-rose-150 card-shadow space-y-6">
                <div>
                    <h3 class="text-sm font-black text-rose-600 uppercase tracking-wide">Danger Zone</h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Terminating store access completely.</p>
                </div>

                <div class="space-y-4 p-4.5 bg-rose-50/50 rounded-2xl border border-rose-100 text-[10px] text-rose-900 font-bold leading-normal flex gap-2.5 items-start">
                    <span>⚠️</span>
                    <span>Deleting this store will drop its tenant SQL database, delete all related domains, and clear the data completely. This action cannot be undone!</span>
                </div>

                <!-- Delete Form -->
                <form action="/admin/tenants/{{ $tenant->id }}/delete" method="POST" onsubmit="return confirmDeleteStore();">
                    @csrf
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs py-3 rounded-xl transition shadow-md shadow-rose-500/10 flex items-center justify-center gap-1.5">
                        <span>Delete Store Permanently 🗑️</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<script>
    function confirmDeleteStore() {
        const confirm1 = confirm("Are you absolutely sure you want to permanently delete this tenant store? This drops the database and is irreversible!");
        if (confirm1) {
            const code = prompt("Please type the Store ID '{{ $tenant->id }}' below to confirm permanent deletion:");
            return code === '{{ $tenant->id }}';
        }
        return false;
    }
</script>
@endsection
