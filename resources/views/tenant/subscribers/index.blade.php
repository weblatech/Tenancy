<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Newsletter Subscribers</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased bg-slate-50/50 pb-16 relative overflow-x-hidden">
    
    <div class="absolute inset-0 dotted-overlay opacity-30 pointer-events-none z-0"></div>
    
    <!-- Top Premium Navigation Bar -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md relative z-15">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <!-- Left Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-600 text-white p-2.5 rounded-xl shadow-lg shadow-emerald-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-emerald-400 text-[10px] font-bold block uppercase tracking-wider">Newsletter Marketing</span>
                    </div>
                </div>
                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="http://{{ $tenantId }}.localhost:8000" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition">View Storefront ↗</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto mt-10 px-6">
        
        <!-- Success/Error Alert -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">✅</span>
                <span class="text-xs font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">⚠️</span>
                <span class="text-xs font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Grid layout: Compose broadcast on left, subscriber list on right (or stacked on mobile) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Column 1 & 2: Broadcast campaign compose -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="relative overflow-hidden bg-slate-950 border border-slate-800 rounded-3xl p-6 shadow-xl text-white">
                    <div class="absolute top-0 left-0 w-32 h-[4px] bg-teal-500"></div>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-2xl bg-indigo-600/30 p-2.5 rounded-2xl text-white">📢</span>
                        <div>
                            <h3 class="text-base font-extrabold tracking-tight">Create Promotional Broadcast</h3>
                            <p class="text-indigo-200/60 text-xs mt-0.5 font-semibold">Send a marketing email to all active newsletter subscribers.</p>
                        </div>
                    </div>

                    <form action="/shop/subscribers/broadcast" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-indigo-300/80 uppercase tracking-widest mb-1.5">Campaign Subject</label>
                            <input type="text" name="subject" required placeholder="e.g. Exclusive Weekend Sale - Up to 50% Off! 🎁" class="w-full px-4 py-3 bg-slate-900/50 border border-indigo-900/60 focus:border-indigo-500 rounded-xl text-xs font-bold text-white outline-none transition placeholder-slate-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-indigo-300/80 uppercase tracking-widest mb-1.5">Email Message Body (Rich Text / Plain Text)</label>
                            <textarea name="message" rows="8" required placeholder="Dear valued subscriber,&#10;&#10;We are thrilled to announce our biggest sale of the season! For this weekend only, use promo code WEEKEND50 at checkout to save 50% off on all products.&#10;&#10;Happy Shopping!&#10;{{ tenant('name') ?? strtoupper($tenantId) }}" class="w-full px-4 py-3 bg-slate-900/50 border border-indigo-900/60 focus:border-indigo-500 rounded-xl text-xs font-semibold text-white outline-none transition placeholder-slate-500 leading-relaxed"></textarea>
                        </div>

                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-[10px] text-indigo-300/60 font-bold uppercase tracking-wider">⚠️ Emails will be sent instantly.</span>
                            <button type="submit" class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-xs font-black px-6 py-3.5 rounded-xl transition duration-200 shadow-lg shadow-indigo-600/25 flex items-center gap-1.5 shrink-0">
                                🚀 Blast Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Column 3: Subscriber list -->
            <div class="space-y-6">
                
                <div class="card-premium relative overflow-hidden rounded-3xl p-6 shadow-sm flex flex-col h-full">
                    <div class="absolute top-0 left-0 w-20 h-[3px] bg-teal-500"></div>
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4 shrink-0">
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Subscribers</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">Marketing audience size</p>
                        </div>
                        <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full uppercase">{{ count($subscribers) }} Emails</span>
                    </div>

                    <!-- Subscriber scroll container -->
                    <div class="flex-1 overflow-y-auto max-h-[420px] custom-scrollbar pr-1">
                        @if(count($subscribers) > 0)
                            <div class="space-y-3">
                                @foreach($subscribers as $sub)
                                    <div class="p-4 bg-slate-50/50 hover:bg-slate-50 border border-slate-200/60 rounded-2xl flex items-center justify-between transition group">
                                        <div class="leading-tight truncate mr-2">
                                            <div class="text-xs font-bold text-slate-800 truncate" title="{{ $sub['email'] }}">{{ $sub['email'] }}</div>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-[9px] text-slate-450 font-medium">{{ date('M d, Y', strtotime($sub['created_at'])) }}</span>
                                                <span class="text-[8px] bg-slate-200/80 text-slate-550 px-1.5 py-0.5 rounded font-mono font-bold">{{ $sub['ip'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <a href="/shop/subscribers/delete/{{ $sub['original_index'] }}" 
                                            onclick="return confirm('Are you sure you want to delete this subscriber?');" 
                                            class="bg-white hover:bg-rose-50 text-slate-350 hover:text-rose-600 transition p-2 rounded-xl border border-slate-200 shadow-sm shrink-0 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-20 bg-slate-50/20 rounded-2xl border-2 border-dashed border-slate-250">
                                <span class="text-4xl mb-3 block">📧</span>
                                <h3 class="text-xs font-extrabold text-slate-800">No subscribers</h3>
                                <p class="text-[10px] text-slate-400 mt-1 max-w-[180px] mx-auto leading-relaxed">No customers have subscribed to your newsletter yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
