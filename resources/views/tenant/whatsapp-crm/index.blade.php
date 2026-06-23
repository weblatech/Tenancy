<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp CRM - Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        }
        .input-premium {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .input-premium:focus {
            border-color: #25D366;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
            outline: none;
        }
        .toggle-switch { position: relative; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1; transition: 0.3s; border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute; content: "";
            height: 18px; width: 18px; left: 3px; bottom: 3px;
            background-color: white; transition: 0.3s; border-radius: 50%;
        }
        input:checked + .toggle-slider { background-color: #25D366; }
        input:checked + .toggle-slider:before { transform: translateX(20px); }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased pb-20">

    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-green-500 to-emerald-500 text-white p-2.5 rounded-xl shadow-lg shadow-green-500/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-green-400 text-[10px] font-bold block uppercase tracking-wider">WhatsApp CRM</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/shop/whatsapp-chat" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Chat</span>
                    </a>
                    <a href="/shop/whatsapp-logs" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Logs</span>
                    </a>
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 px-6">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">WhatsApp CRM Settings</h1>
            <p class="text-slate-500 font-medium text-sm mt-1">Configure WhatsApp Business API for your store</p>
        </div>

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

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            @if(!$isConfigured)
            <a href="/shop/whatsapp-register" class="card-premium rounded-2xl p-5 flex items-center gap-4 hover:border-green-300 transition group border-2 border-dashed border-green-300 bg-green-50/50">
                <div class="bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20 group-hover:scale-105 transition pulse-green">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-green-800">Connect WhatsApp</h3>
                    <p class="text-[10px] text-green-600 font-medium">Register your phone number</p>
                </div>
            </a>
            @endif
            <a href="/shop/whatsapp-chat" class="card-premium rounded-2xl p-5 flex items-center gap-4 hover:border-green-300 transition group">
                <div class="bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">Chat with Customers</h3>
                    <p class="text-[10px] text-slate-400 font-medium">View conversations & reply</p>
                </div>
            </a>
            <a href="/shop/whatsapp-logs" class="card-premium rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 transition group">
                <div class="bg-blue-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">Message Logs</h3>
                    <p class="text-[10px] text-slate-400 font-medium">All sent & received messages</p>
                </div>
            </a>
            <div class="card-premium rounded-2xl p-5 flex items-center gap-4 bg-slate-50">
                <div class="{{ $isConfigured ? 'bg-green-600' : 'bg-slate-400' }} text-white w-12 h-12 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-sm text-slate-900">API Status</h3>
                    <p class="text-[10px] {{ $isConfigured ? 'text-green-600' : 'text-rose-500' }} font-bold">{{ $isConfigured ? 'Cloud API Connected' : 'Not Configured' }}</p>
                </div>
            </div>
        </div>

        <form action="/shop/whatsapp-crm" method="POST">
            @csrf

            <!-- Enable/Disable CRM -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-green-500"></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-green-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-lg text-slate-900">WhatsApp Automation</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Enable automated order notifications</p>
                        </div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="whatsapp_crm_active" value="1" {{ $settings->whatsapp_crm_active ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                @if(!$isConfigured)
                <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-xs text-amber-800 font-bold">API Not Configured</p>
                        @if(!$hasProvider)
                            <p class="text-[10px] text-amber-700 mt-1"><strong>Step 1:</strong> Super Admin must save WhatsApp Provider at <a href="/admin/whatsapp-provider" class="underline font-bold">/admin/whatsapp-provider</a></p>
                        @else
                            <p class="text-[10px] text-amber-700 mt-1"><strong>Step 2:</strong> Save your Phone Number ID below and toggle "WhatsApp Automation" ON.</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- WhatsApp Number Configuration -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-blue-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">WhatsApp Number</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Your store's WhatsApp for sending messages</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Phone Number ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_phone_number_id" value="{{ $settings->whatsapp_phone_number_id ?? '' }}" placeholder="e.g. 1234567890" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">From Meta Cloud API dashboard</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Webhook Verify Token</label>
                        <input type="text" name="whatsapp_verify_token" value="{{ $settings->whatsapp_verify_token ?? '' }}" placeholder="e.g. my_store_verify_123" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">Must match your Meta app webhook config</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 mb-2 uppercase tracking-wider">Store Display Number</label>
                        <input type="text" name="footer_whatsapp" value="{{ $settings->footer_whatsapp ?? '' }}" placeholder="e.g. 03001234567" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 placeholder-slate-400">
                        <p class="text-[10px] text-slate-400 mt-1">WhatsApp number shown on storefront</p>
                    </div>
                </div>
            </div>

            <!-- Message Templates -->
            <div class="card-premium rounded-3xl p-6 md:p-8 mb-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-24 h-[3px] bg-emerald-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-emerald-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900">Message Templates</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customize messages for each order status</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-amber-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            New Order Pending (with Confirm/Cancel buttons)
                        </label>
                        <textarea name="whatsapp_msg_order_pending" rows="4" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_pending ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {customer_name}, {order_id}, {store_name}, {items}, {total}, {address}, {phone}, {payment_method}</p>
                    </div>

                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-green-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Order Confirmed
                        </label>
                        <textarea name="whatsapp_msg_order_confirmed" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_confirmed ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {customer_name}, {order_id}, {store_name}, {total}</p>
                    </div>

                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-blue-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Order Processing / Shipped
                        </label>
                        <textarea name="whatsapp_msg_order_processing" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_processing ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {customer_name}, {order_id}, {store_name}</p>
                    </div>

                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-indigo-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                            Order Completed / Delivered
                        </label>
                        <textarea name="whatsapp_msg_order_completed" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_completed ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {customer_name}, {order_id}, {store_name}, {total}</p>
                    </div>

                    <div class="bg-white border border-slate-200 p-4 rounded-2xl">
                        <label class="block text-xs font-black text-rose-600 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                            Order Cancelled
                        </label>
                        <textarea name="whatsapp_msg_order_cancelled" rows="3" class="input-premium w-full px-4 py-3 rounded-xl text-sm font-medium text-slate-700 placeholder-slate-400 resize-none" placeholder="Leave empty for default message...">{{ $settings->whatsapp_msg_order_cancelled ?? '' }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Variables: {customer_name}, {order_id}, {store_name}</p>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <p class="text-xs text-slate-600 font-semibold leading-relaxed">
                        <strong>Tip:</strong> Leave templates empty to use the system default messages. The "Order Pending" message automatically includes Confirm and Cancel buttons.
                    </p>
                </div>
            </div>

            <!-- Save Button -->
            <div class="sticky bottom-0 bg-white/80 backdrop-blur-xl border-t border-slate-200 -mx-6 px-6 py-4 rounded-b-3xl">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-medium">Changes are saved per-tenant</p>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-black text-sm px-8 py-3 rounded-xl transition duration-200 shadow-md shadow-green-600/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </div>
            </div>
        </form>

    </div>

</body>
</html>
