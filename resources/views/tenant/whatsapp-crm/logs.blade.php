<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Logs - Store Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-premium {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased pb-20">

    <!-- Top Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-tr from-green-500 to-emerald-500 text-white p-2.5 rounded-xl shadow-lg shadow-green-500/20">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-green-400 text-[10px] font-bold block uppercase tracking-wider">WhatsApp Logs</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/shop/whatsapp-chat" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Chat</span>
                    </a>
                    <a href="/shop/whatsapp-crm" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Settings</span>
                    </a>
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-10 px-6">

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">WhatsApp Message Logs</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Track all sent and received WhatsApp messages</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card-premium rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-slate-900">{{ $stats['total'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total Messages</p>
            </div>
            <div class="card-premium rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-green-600">{{ $stats['sent'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Sent</p>
            </div>
            <div class="card-premium rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-blue-600">{{ $stats['inbound'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Received</p>
            </div>
            <div class="card-premium rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-rose-600">{{ $stats['failed'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Failed</p>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card-premium rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal text-left text-xs font-semibold text-slate-600">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-extrabold uppercase tracking-wider">
                            <th class="px-5 py-4">Direction</th>
                            <th class="px-5 py-4">Type</th>
                            <th class="px-5 py-4">To</th>
                            <th class="px-5 py-4">Message</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Auto</th>
                            <th class="px-5 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/40 transition">
                                <td class="px-5 py-4">
                                    @if($log->direction === 'outbound')
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-100 text-[9px] px-2 py-0.5 rounded-full font-black uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            Out
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-100 text-[9px] px-2 py-0.5 rounded-full font-black uppercase">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                            In
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[10px] font-black uppercase tracking-wider
                                        {{ str_contains($log->message_type, 'pending') ? 'text-amber-600 bg-amber-50 px-2 py-0.5 rounded' : '' }}
                                        {{ str_contains($log->message_type, 'confirmed') ? 'text-green-600 bg-green-50 px-2 py-0.5 rounded' : '' }}
                                        {{ str_contains($log->message_type, 'processing') ? 'text-blue-600 bg-blue-50 px-2 py-0.5 rounded' : '' }}
                                        {{ str_contains($log->message_type, 'completed') ? 'text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded' : '' }}
                                        {{ str_contains($log->message_type, 'cancelled') ? 'text-rose-600 bg-rose-50 px-2 py-0.5 rounded' : '' }}
                                        {{ $log->message_type === 'manual_chat' ? 'text-purple-600 bg-purple-50 px-2 py-0.5 rounded' : '' }}
                                        {{ $log->message_type === 'text' ? 'text-slate-600 bg-slate-50 px-2 py-0.5 rounded' : '' }}
                                    ">{{ str_replace('_', ' ', $log->message_type) }}</span>
                                </td>
                                <td class="px-5 py-4 font-mono text-[10px]">{{ $log->to_phone }}</td>
                                <td class="px-5 py-4 max-w-xs truncate text-[10px]">{{ substr($log->message_body, 0, 60) }}{{ strlen($log->message_body) > 60 ? '...' : '' }}</td>
                                <td class="px-5 py-4">
                                    @if($log->status === 'sent')
                                        <span class="inline-flex items-center gap-1 text-green-600 text-[9px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Sent
                                        </span>
                                    @elseif($log->status === 'failed')
                                        <span class="inline-flex items-center gap-1 text-rose-600 text-[9px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span> Failed
                                        </span>
                                    @elseif($log->status === 'received')
                                        <span class="inline-flex items-center gap-1 text-blue-600 text-[9px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Received
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-slate-400 text-[9px] font-black uppercase">
                                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> {{ $log->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($log->is_auto)
                                        <span class="text-[8px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded uppercase">Auto</span>
                                    @else
                                        <span class="text-[8px] font-black text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded uppercase">Manual</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-[10px] text-slate-400">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') : '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400 font-medium">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    No WhatsApp messages logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>
