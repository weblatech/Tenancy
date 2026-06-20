<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin Dashboard') — MUNAA SaaS</title>
    <meta name="description" content="MUNAA SaaS Super Admin Control Panel — Manage stores, tenants, and platform analytics.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        /* ── Base ───────────────────────────────────────── */
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #f8fafc;
        }

        /* ── Dot-grid overlay for workspace ─────────────── */
        .dot-grid {
            background-image: radial-gradient(circle, rgba(148,163,184,0.18) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        /* ── Sidebar active glow ────────────────────────── */
        .sidebar-link {
            position: relative;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link:hover {
            transform: translateX(5px);
        }
        .sidebar-link-active {
            background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(139,92,246,0.10) 100%);
            color: #a5b4fc;
            box-shadow:
                inset 3px 0 0 0 #818cf8,
                -4px 0 18px -2px rgba(99,102,241,0.35),
                0 0 28px -4px rgba(99,102,241,0.18);
            animation: active-pulse 2.8s ease-in-out infinite;
        }
        .sidebar-link-active svg {
            filter: drop-shadow(0 0 6px rgba(129,140,248,0.6));
        }

        @keyframes active-pulse {
            0%, 100% { box-shadow: inset 3px 0 0 0 #818cf8, -4px 0 18px -2px rgba(99,102,241,0.35), 0 0 28px -4px rgba(99,102,241,0.18); }
            50%      { box-shadow: inset 3px 0 0 0 #a5b4fc, -4px 0 26px -2px rgba(99,102,241,0.50), 0 0 40px -4px rgba(99,102,241,0.28); }
        }

        /* ── Stat card (dashboard metrics) ──────────────── */
        .stat-card {
            background: #ffffff;
            border: 1px solid rgba(226,232,240,0.8);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow:
                0 1px 3px rgba(0,0,0,0.04),
                0 8px 32px -8px rgba(99,102,241,0.08);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow:
                0 4px 8px rgba(0,0,0,0.06),
                0 16px 48px -8px rgba(99,102,241,0.16);
        }

        /* ── Glass card ─────────────────────────────────── */
        .glass-card {
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.45);
            border-radius: 1rem;
            box-shadow:
                0 4px 24px -4px rgba(99,102,241,0.08),
                inset 0 1px 0 rgba(255,255,255,0.6);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 8px 40px -6px rgba(99,102,241,0.14),
                inset 0 1px 0 rgba(255,255,255,0.7);
        }

        /* ── Toast glassmorphism ────────────────────────── */
        .toast-glass {
            backdrop-filter: blur(18px) saturate(200%);
            -webkit-backdrop-filter: blur(18px) saturate(200%);
            animation: toast-in 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(-12px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Sidebar scrollbar ──────────────────────────── */
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.2); border-radius: 10px; }

        /* ── Header gradient shimmer ────────────────────── */
        .brand-gradient {
            background: linear-gradient(135deg, #020617 0%, #0f172a 50%, #020617 100%);
            background-size: 200% 200%;
            animation: gradient-shift 8s ease-in-out infinite;
        }
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        /* ── Live clock ─────────────────────────────────── */
        #live-clock { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex">

    <!-- ─── Left Sidebar ──────────────────────────────── -->
    <aside class="w-[270px] bg-slate-950 text-slate-400 flex flex-col fixed inset-y-0 z-50 shrink-0 border-r border-slate-800/60">

        <!-- Brand Header -->
        <div class="brand-gradient px-6 py-5 border-b border-slate-800/50">
            <div class="flex items-center gap-3.5">
                <!-- Glowing icon -->
                <div class="relative">
                    <div class="absolute inset-0 bg-indigo-500 rounded-xl blur-md opacity-40"></div>
                    <div class="relative bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="text-white font-extrabold text-[15px] tracking-tight block leading-tight">MUNAA</span>
                    <span class="text-indigo-400/80 text-[9px] font-bold uppercase tracking-[0.2em] block mt-0.5">Super Admin Panel</span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav flex-grow py-6 px-4 space-y-1 overflow-y-auto">
            <!-- Section Label -->
            <div class="px-3 pb-3">
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-600">Main Menu</span>
            </div>

            <a href="/admin"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-semibold {{ Request::is('admin') ? 'sidebar-link-active' : 'hover:bg-slate-800/70 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/>
                </svg>
                <span>Dashboard Analytics</span>
            </a>

            <a href="/admin/tenants"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-semibold {{ Request::is('admin/tenants') || Request::is('admin/tenants/*') && !Request::is('admin/tenants/create') ? 'sidebar-link-active' : 'hover:bg-slate-800/70 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Manage Stores</span>
            </a>

            <a href="/admin/tenants/create"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-semibold {{ Request::is('admin/tenants/create') ? 'sidebar-link-active' : 'hover:bg-slate-800/70 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Add New Store</span>
            </a>

            <div class="px-3 pt-4 pb-2">
                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-600">Integrations</span>
            </div>

            <a href="/admin/whatsapp-provider"
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-semibold {{ Request::is('admin/whatsapp-provider') ? 'sidebar-link-active' : 'hover:bg-slate-800/70 hover:text-slate-200' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span>WhatsApp Provider</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800/50 space-y-3">
            <div class="flex items-center gap-3 px-2">
                <!-- User avatar -->
                <div class="relative flex-shrink-0">
                    <div class="absolute inset-0 bg-indigo-500 rounded-full blur-sm opacity-30"></div>
                    <div class="relative bg-gradient-to-br from-indigo-500 to-violet-600 text-white rounded-full w-9 h-9 flex items-center justify-center font-extrabold text-xs shadow-lg shadow-indigo-500/20">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
                <div class="overflow-hidden min-w-0">
                    <span class="text-[13px] font-bold text-slate-200 block truncate leading-tight">{{ Auth::user()->name ?? 'System Admin' }}</span>
                    <span class="text-[10px] font-medium text-slate-500 block truncate">{{ Auth::user()->email ?? '' }}</span>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="/"
                   class="flex-grow flex items-center justify-center gap-1.5 bg-slate-800/80 hover:bg-indigo-600 text-slate-400 hover:text-white text-center text-[10px] font-bold py-2.5 rounded-lg transition-all duration-200 uppercase tracking-wider border border-slate-700/50 hover:border-indigo-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    Storefront
                </a>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-10 h-full bg-slate-800/80 hover:bg-rose-950 text-slate-500 hover:text-rose-400 flex items-center justify-center rounded-lg transition-all duration-200 border border-slate-700/50 hover:border-rose-800"
                            title="Log Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ─── Right Workspace ───────────────────────────── -->
    <div class="flex-grow ml-[270px] flex flex-col min-h-screen">

        <!-- Top Header Bar -->
        <header class="h-16 bg-white/80 backdrop-blur-lg border-b border-slate-200/70 px-8 flex items-center justify-between sticky top-0 z-40">
            <h1 class="text-sm font-extrabold text-slate-900 tracking-wide uppercase">
                @yield('page_title', 'System Administrator')
            </h1>
            <div class="flex items-center gap-4">
                <!-- Live date/time -->
                <div class="text-right hidden sm:block">
                    <span id="live-clock" class="text-[13px] font-semibold text-slate-700 block leading-tight"></span>
                    <span id="live-date" class="text-[10px] font-medium text-slate-400 block"></span>
                </div>
                <!-- System status badge -->
                <div class="flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-[10px] px-3 py-1.5 rounded-full font-bold border border-emerald-200/80 shadow-sm shadow-emerald-500/5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    System Online
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow p-8 bg-slate-50/80 dot-grid">

            <!-- ─ Toast: Success ──────────────────────── -->
            @if(session('success'))
                <div class="toast-glass mb-8 bg-emerald-50/70 border border-emerald-200/60 text-emerald-950 px-6 py-4 rounded-2xl font-bold flex items-center gap-3.5 shadow-lg shadow-emerald-500/8">
                    <span class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-full p-1.5 shadow-md shadow-emerald-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div class="min-w-0">
                        <span class="text-[11px] font-extrabold text-emerald-900 block">Success</span>
                        <span class="text-[13px] font-semibold text-emerald-700 block truncate">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('div.toast-glass').remove()" class="ml-auto text-emerald-400 hover:text-emerald-700 transition flex-shrink-0 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- ─ Toast: Error ────────────────────────── -->
            @if(session('error'))
                <div class="toast-glass mb-8 bg-rose-50/70 border border-rose-200/60 text-rose-950 px-6 py-4 rounded-2xl font-bold flex items-center gap-3.5 shadow-lg shadow-rose-500/8">
                    <span class="bg-gradient-to-br from-rose-500 to-red-600 text-white rounded-full p-1.5 shadow-md shadow-rose-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                    <div class="min-w-0">
                        <span class="text-[11px] font-extrabold text-rose-900 block">Error</span>
                        <span class="text-[13px] font-semibold text-rose-700 block truncate">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('div.toast-glass').remove()" class="ml-auto text-rose-400 hover:text-rose-700 transition flex-shrink-0 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- ─── Live Clock Script ─────────────────────────── -->
    <script>
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            const date = now.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });
            const clockEl = document.getElementById('live-clock');
            const dateEl = document.getElementById('live-date');
            if (clockEl) clockEl.textContent = time;
            if (dateEl) dateEl.textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Auto-dismiss toasts after 6 seconds
        document.querySelectorAll('.toast-glass').forEach(function(toast) {
            setTimeout(function() {
                toast.style.transition = 'opacity 0.4s, transform 0.4s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px) scale(0.98)';
                setTimeout(function() { toast.remove(); }, 400);
            }, 6000);
        });
    </script>

</body>
</html>
