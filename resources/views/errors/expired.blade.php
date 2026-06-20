<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired — MUNAA SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@350;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.12; transform: scale(1); }
            50% { opacity: 0.22; transform: scale(1.05); }
        }
        .glow-pulse {
            animation: pulse-glow 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 antialiased flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Glowing amber/orange lights background -->
    <div class="absolute top-[-20%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-amber-500/10 blur-[120px] pointer-events-none glow-pulse"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-orange-500/8 blur-[120px] pointer-events-none glow-pulse" style="animation-delay: 3s;"></div>
    <div class="absolute top-[30%] right-[10%] w-[25vw] h-[25vw] rounded-full bg-yellow-500/5 blur-[100px] pointer-events-none"></div>

    <!-- Container -->
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-md border border-slate-700/80 rounded-[2.5rem] p-8 text-center shadow-2xl relative z-10 space-y-6">
        
        <!-- Icon -->
        <div class="w-20 h-20 bg-amber-500/15 text-amber-500 rounded-[2rem] flex items-center justify-center text-4xl mx-auto shadow-lg shadow-amber-500/10 border border-amber-500/25">
            ⏰
        </div>

        <!-- Warning Title -->
        <div class="space-y-2">
            <h2 class="text-2xl font-black tracking-tight text-white block">Subscription Expired</h2>
            <h3 class="text-lg font-bold text-amber-400 block tracking-normal">سبسکرپشن ختم ہو گئی ہے</h3>
        </div>

        <!-- Divider -->
        <div class="h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent w-full"></div>

        <!-- Description -->
        <div class="space-y-4 text-xs font-semibold leading-relaxed text-slate-350">
            <p>
                The subscription for <span class="text-amber-300 font-bold">{{ $tenant->name ?? 'This store' }}</span> has expired. Access to the storefront and all merchant features has been temporarily restricted until the subscription is renewed.
            </p>
            <p class="text-[10px] text-slate-400 font-medium bg-slate-850 p-4.5 rounded-2xl border border-slate-800">
                ⚠️ If you are the store owner, please renew your subscription or contact the platform administration team to restore full access to your store.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="pt-4 flex justify-center gap-3">
            <a href="mailto:support@munaa.com" class="bg-amber-600 hover:bg-amber-500 text-white font-extrabold text-xs px-6 py-3 rounded-xl transition border border-amber-500/50 shadow-lg shadow-amber-600/20">
                Contact Support
            </a>
            <a href="http://localhost:8000" class="bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white font-extrabold text-xs px-6 py-3 rounded-xl transition border border-slate-700">
                Platform Home
            </a>
        </div>

    </div>

</body>
</html>
