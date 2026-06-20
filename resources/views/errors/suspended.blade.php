<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Suspended — MUNAA SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@350;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 antialiased flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Glowing lights background -->
    <div class="absolute top-[-20%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-rose-500/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-amber-500/5 blur-[120px] pointer-events-none"></div>

    <!-- Container -->
    <div class="max-w-md w-full bg-slate-800/80 backdrop-blur-md border border-slate-700/80 rounded-[2.5rem] p-8 text-center shadow-2xl relative z-10 space-y-6">
        
        <!-- Icon -->
        <div class="w-20 h-20 bg-rose-500/15 text-rose-500 rounded-[2rem] flex items-center justify-center text-4xl mx-auto shadow-lg shadow-rose-500/10 border border-rose-500/25">
            🛑
        </div>

        <!-- Warning Title -->
        <div class="space-y-2">
            <h2 class="text-2xl font-black tracking-tight text-white block">Store Suspended</h2>
            <h3 class="text-lg font-bold text-rose-400 block tracking-normal">اسٹور معطل کر دیا گیا ہے</h3>
        </div>

        <!-- Divider -->
        <div class="h-px bg-slate-700/50 w-full"></div>

        <!-- Description -->
        <div class="space-y-4 text-xs font-semibold leading-relaxed text-slate-350">
            <p>
                The platform administrator has temporarily suspended access to this store. Public storefront and merchant admin panel routes are locked.
            </p>
            <p class="text-[10px] text-slate-400 font-medium bg-slate-850 p-4.5 rounded-2xl border border-slate-800">
                📢 If you are the store owner, please contact the platform administration team to review billing status or resolve violation flags.
            </p>
        </div>

        <!-- Contact Support -->
        <div class="pt-4 flex justify-center gap-3">
            <a href="mailto:support@munaa.com" class="bg-slate-700 hover:bg-slate-650 text-white font-extrabold text-xs px-6 py-3 rounded-xl transition border border-slate-600/50">
                Contact Support
            </a>
            <a href="http://localhost:8000" class="bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white font-extrabold text-xs px-6 py-3 rounded-xl transition border border-slate-700">
                Platform Home
            </a>
        </div>

    </div>

</body>
</html>
