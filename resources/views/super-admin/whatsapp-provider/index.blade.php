@extends('super-admin.layout')

@section('title', 'WhatsApp Provider Settings')
@section('page_title', 'WhatsApp Business API Provider')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800 font-bold text-sm shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3 text-rose-800 font-bold text-sm shadow-sm">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="/admin/whatsapp-provider" method="POST">
        @csrf

        <!-- API Provider Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 mb-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-green-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-lg text-slate-900">WhatsApp Business API Provider</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Global API configuration for all stores</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">API Provider</label>
                    <select name="provider_name" class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition">
                        <option value="meta" {{ ($provider->provider_name ?? '') === 'meta' ? 'selected' : '' }}>Meta (WhatsApp Business Platform)</option>
                        <option value="twilio" {{ ($provider->provider_name ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Status</label>
                    <div class="flex items-center gap-3 h-[46px]">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ ($provider->is_active ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                        <span class="text-xs font-bold text-slate-500">{{ ($provider->is_active ?? false) ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">API Key (Access Token)</label>
                    <input type="password" name="api_key" value="{{ $provider->api_key ?? '' }}" placeholder="EAAxxxxxxx..." class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Phone Number ID</label>
                    <input type="text" name="phone_number_id" value="{{ $provider->phone_number_id ?? '' }}" placeholder="1234567890" class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Business Account ID</label>
                    <input type="text" name="business_account_id" value="{{ $provider->business_account_id ?? '' }}" placeholder="Optional" class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Verify Token</label>
                    <input type="text" name="verify_token" value="{{ $provider->verify_token ?? '' }}" placeholder="Your custom verify token" class="w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20 outline-none transition placeholder-slate-400">
                </div>
            </div>

            <div class="mt-4 p-4 bg-green-50/60 border border-green-100/60 rounded-2xl">
                <p class="text-xs text-green-800 font-semibold leading-relaxed">
                    <strong>Setup Instructions:</strong> Create a WhatsApp Business API app at <a href="https://developers.facebook.com/" target="_blank" class="underline">developers.facebook.com</a>. Get your Access Token and Phone Number ID from the app dashboard. Each store owner will use this shared API connection with their own WhatsApp phone number.
                </p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-black text-sm px-8 py-3 rounded-xl transition duration-200 shadow-md shadow-green-600/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Provider Settings
            </button>
        </div>
    </form>

    <!-- Store Owners WhatsApp Status -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8 mt-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <div class="bg-indigo-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h3 class="font-black text-lg text-slate-900">Store Owners WhatsApp Status</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Per-tenant WhatsApp CRM status</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-100">
            <table class="min-w-full text-xs font-semibold text-slate-600">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-extrabold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left">Store</th>
                        <th class="px-4 py-3 text-left">Phone Number</th>
                        <th class="px-4 py-3 text-center">CRM Status</th>
                        <th class="px-4 py-3 text-center">Messages Sent</th>
                        <th class="px-4 py-3 text-center">Conversations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($storeStatuses as $store)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3">
                                <span class="font-black text-slate-900">{{ $store['name'] }}</span>
                                <span class="text-[9px] text-slate-400 block">{{ $store['tenant_id'] }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px]">{{ $store['phone'] ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($store['crm_active'])
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] px-2 py-0.5 rounded-full font-black uppercase">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-50 text-slate-500 border border-slate-100 text-[9px] px-2 py-0.5 rounded-full font-black uppercase">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-black text-slate-900">{{ $store['messages_sent'] }}</td>
                            <td class="px-4 py-3 text-center font-black text-slate-900">{{ $store['conversations'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-medium">No stores found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
