<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'ShopTenancy') }} - Start & Grow Your Online Store</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Alpine.js via CDN for instant preview/dev, fallback if Vite not compiled -->
        <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            body {
                font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            }
            .font-heading {
                font-family: 'Outfit', sans-serif;
            }
            .shopify-green {
                color: #004c3f;
            }
            .bg-shopify-green {
                background-color: #004c3f;
            }
            .bg-shopify-light-green {
                background-color: #f4fbf7;
            }
            .border-shopify-green {
                border-color: #004c3f;
            }
            .focus-shopify-ring:focus {
                outline: 2px solid transparent;
                outline-offset: 2px;
                --tw-ring-color: #004c3f;
                box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow, 0 0 #0000);
            }
        </style>
    </head>
    <body class="bg-slate-50/50 text-slate-800 selection:bg-[#004c3f] selection:text-white antialiased min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">

        <!-- Announcement Bar -->
        <div class="bg-shopify-green text-white text-center py-2 px-4 text-xs font-semibold tracking-wide">
            🚀 Launch your online store today - Start your 14-day free trial. No credit card required!
        </div>

        <!-- Sticky Navigation Header -->
        <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                
                <!-- Logo -->
                <div class="flex items-center gap-12">
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-shopify-green flex items-center justify-center text-white shadow-md shadow-emerald-950/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold font-heading tracking-tight text-slate-900">{{ config('app.name', 'ShopTenancy') }}</span>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                        <a href="#features" class="hover:text-slate-900 transition-colors">Features</a>
                        <a href="#customizer" class="hover:text-slate-900 transition-colors">Live Customizer</a>
                        <a href="#pricing" class="hover:text-slate-900 transition-colors">Pricing</a>
                        <a href="#faqs" class="hover:text-slate-900 transition-colors">FAQs</a>
                    </nav>
                </div>

                <!-- Desktop CTA Actions -->
                <div class="hidden md:flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 font-semibold text-slate-700 shadow-sm transition-all">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 transition-colors">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-shopify-green hover:bg-[#00382f] text-white font-semibold shadow-md shadow-emerald-950/10 hover:shadow-emerald-950/20 hover:-translate-y-0.5 transition-all">
                                    Start free trial
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button type="button" class="text-slate-600 hover:text-slate-900 p-2 rounded-lg" @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-show="mobileMenuOpen" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div class="md:hidden border-b border-slate-100 bg-white" x-show="mobileMenuOpen" x-transition style="display: none;">
                <div class="px-4 pt-2 pb-6 space-y-3">
                    <a href="#features" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900" @click="mobileMenuOpen = false">Features</a>
                    <a href="#customizer" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900" @click="mobileMenuOpen = false">Live Customizer</a>
                    <a href="#pricing" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900" @click="mobileMenuOpen = false">Pricing</a>
                    <a href="#faqs" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900" @click="mobileMenuOpen = false">FAQs</a>
                    <hr class="border-slate-100 my-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block text-center w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold shadow-sm hover:bg-slate-50" @click="mobileMenuOpen = false">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block text-center w-full px-4 py-2.5 rounded-xl border border-slate-100 text-slate-700 font-semibold hover:bg-slate-50" @click="mobileMenuOpen = false">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block text-center w-full px-4 py-2.5 rounded-xl bg-shopify-green text-white font-semibold shadow-sm hover:bg-[#00382f]" @click="mobileMenuOpen = false">
                                    Start free trial
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            
            <!-- Hero Section -->
            <section class="relative bg-gradient-to-b from-slate-100 to-white pt-12 pb-24 lg:pt-20 lg:pb-32 overflow-hidden">
                <!-- Background decoration element -->
                <div class="absolute -top-40 right-0 w-[500px] h-[500px] bg-emerald-100/40 rounded-full blur-3xl -z-10"></div>
                <div class="absolute bottom-0 left-10 w-[300px] h-[300px] bg-slate-200/50 rounded-full blur-2xl -z-10"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                        
                        <!-- Left Content -->
                        <div class="lg:col-span-6 space-y-8 text-center lg:text-left">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold tracking-wide">
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                Next-Gen Multi-Tenant Platform
                            </div>

                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-heading text-slate-900 tracking-tight leading-[1.1]">
                                Bring your business online with <span class="text-shopify-green">{{ config('app.name', 'ShopTenancy') }}</span>
                            </h1>

                            <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0">
                                Launch a fully functional, lightning-fast e-commerce storefront. Design it dynamically, manage products in isolation, and scale your brand with state-of-the-art security.
                            </p>

                            <!-- CTA Form / Dashboard Redirect -->
                            <div class="max-w-md mx-auto lg:mx-0">
                                @auth
                                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-shopify-green hover:bg-[#00382f] text-white font-bold text-base shadow-lg shadow-emerald-950/20 hover:shadow-emerald-950/30 hover:-translate-y-0.5 transition-all">
                                            Go to Merchant Dashboard
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 ml-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                            </svg>
                                        </a>
                                    </div>
                                @else
                                    <form action="{{ route('register') }}" method="GET" class="flex flex-col sm:flex-row gap-3 p-1.5 rounded-2xl bg-white border border-slate-200 shadow-xl shadow-slate-100 focus-within:border-shopify-green transition-colors">
                                        <input type="email" name="email" placeholder="Enter your email address" required 
                                               class="flex-grow px-5 py-3.5 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none text-sm bg-transparent">
                                        <button type="submit" class="bg-shopify-green hover:bg-[#00382f] text-white font-bold text-sm px-7 py-3.5 rounded-xl transition-all shadow-md shadow-emerald-950/10 hover:shadow-emerald-950/20 whitespace-nowrap">
                                            Start free trial
                                        </button>
                                    </form>
                                @endauth
                                <p class="text-xs text-slate-500 mt-3 text-center lg:text-left">
                                    Try free for 14 days. No setup fees. Cancel anytime.
                                </p>
                            </div>
                        </div>

                        <!-- Right Graphic Mockup -->
                        <div class="lg:col-span-6 relative flex justify-center">
                            <!-- Floating Card 1 -->
                            <div class="absolute -left-6 top-1/4 bg-white/95 backdrop-blur-sm p-4 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-3 animate-bounce" style="animation-duration: 6s;">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Sales</div>
                                    <div class="text-base font-extrabold text-slate-900">$12,450.80</div>
                                </div>
                            </div>

                            <!-- Floating Card 2 -->
                            <div class="absolute -right-6 bottom-1/4 bg-white/95 backdrop-blur-sm p-4 rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center gap-3 animate-bounce" style="animation-duration: 8s; animation-delay: 1s;">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">New Store</div>
                                    <div class="text-base font-extrabold text-slate-900">Created Live</div>
                                </div>
                            </div>

                            <!-- Main Mockup Container -->
                            <div class="w-full max-w-[500px] aspect-[4/3] rounded-3xl bg-slate-900 shadow-2xl shadow-slate-900/30 border border-slate-800 p-3 overflow-hidden flex flex-col relative">
                                
                                <!-- Mock Browser Bar -->
                                <div class="flex items-center justify-between pb-3 px-2 border-b border-slate-800">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                                    </div>
                                    <div class="bg-slate-800 rounded-lg text-[10px] font-medium text-slate-400 px-12 py-1 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3 h-3 text-slate-500">
                                            <path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd" />
                                        </svg>
                                        admin.yourstore.com
                                    </div>
                                    <div class="w-9"></div>
                                </div>

                                <!-- Mock Store Builder Grid -->
                                <div class="flex-grow grid grid-cols-12 gap-3 pt-3 h-full overflow-hidden">
                                    
                                    <!-- Left sidebar mockup controls -->
                                    <div class="col-span-4 bg-slate-950/50 rounded-xl p-3 border border-slate-800/80 flex flex-col gap-2 overflow-hidden select-none">
                                        <div class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Store Customizer</div>
                                        <div class="w-full h-4 bg-slate-800 rounded-md flex items-center justify-between px-2 text-[8px] text-slate-300">
                                            <span>Announcement Bar</span>
                                            <div class="w-4 h-2 bg-emerald-500 rounded-full"></div>
                                        </div>
                                        <div class="w-full h-4 bg-slate-800 rounded-md flex items-center justify-between px-2 text-[8px] text-slate-300">
                                            <span>Header & Menu</span>
                                            <svg class="w-2 h-2 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M7 10l5 5V5z"/></svg>
                                        </div>
                                        <div class="w-full h-8 bg-slate-900 rounded-md border border-slate-700/50 flex flex-col gap-1 p-1">
                                            <div class="w-8 h-1 bg-slate-700 rounded-full"></div>
                                            <div class="w-12 h-1 bg-slate-700 rounded-full"></div>
                                        </div>
                                        <div class="w-full h-4 bg-slate-800 rounded-md flex items-center justify-between px-2 text-[8px] text-slate-300">
                                            <span>Hero Banner</span>
                                            <svg class="w-2 h-2 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M7 10l5 5V5z"/></svg>
                                        </div>
                                        <div class="w-full h-4 bg-slate-800 rounded-md flex items-center justify-between px-2 text-[8px] text-slate-300">
                                            <span>Footer Settings</span>
                                            <svg class="w-2 h-2 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M7 10l5 5V5z"/></svg>
                                        </div>
                                        <div class="mt-auto w-full h-5 bg-shopify-green rounded-md flex items-center justify-center text-[8px] font-bold text-white">
                                            Save Settings
                                        </div>
                                    </div>

                                    <!-- Right Preview Canvas Mockup -->
                                    <div class="col-span-8 bg-white rounded-xl p-3 border border-slate-800/80 flex flex-col gap-2 overflow-hidden relative shadow-inner">
                                        <!-- Store announcement -->
                                        <div class="w-full bg-[#004c3f] text-white text-[5px] text-center py-0.5 rounded-sm">
                                            🚀 Special Launch Sale: 20% Off!
                                        </div>
                                        <!-- Store header -->
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                            <div class="text-[7px] font-bold text-slate-900">MerchStore</div>
                                            <div class="flex gap-2 text-[5px] text-slate-500 font-medium">
                                                <span>Home</span>
                                                <span>Catalog</span>
                                                <span>About</span>
                                            </div>
                                        </div>
                                        <!-- Store hero -->
                                        <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-center flex flex-col items-center justify-center gap-1.5">
                                            <div class="text-[9px] font-extrabold text-slate-900 leading-tight">Summer Collection Live</div>
                                            <div class="text-[5px] text-slate-500 max-w-[140px]">High-quality products curated just for you.</div>
                                            <div class="px-2 py-0.5 bg-slate-900 text-white rounded text-[4px] font-semibold">Shop Now</div>
                                        </div>
                                        <!-- Grid items preview -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="bg-slate-50 border border-slate-100 rounded p-1.5 flex flex-col gap-1">
                                                <div class="aspect-square bg-slate-200 rounded-sm"></div>
                                                <div class="h-1 w-8 bg-slate-400 rounded-full"></div>
                                                <div class="h-1 w-4 bg-slate-900 rounded-full"></div>
                                            </div>
                                            <div class="bg-slate-50 border border-slate-100 rounded p-1.5 flex flex-col gap-1">
                                                <div class="aspect-square bg-slate-200 rounded-sm"></div>
                                                <div class="h-1 w-8 bg-slate-400 rounded-full"></div>
                                                <div class="h-1 w-4 bg-slate-900 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Social Proof Bar -->
            <section class="border-y border-slate-100 bg-white py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        <div class="space-y-1">
                            <div class="text-3xl font-extrabold text-slate-900 font-heading">15,000+</div>
                            <div class="text-xs uppercase font-bold tracking-wider text-slate-400">Active Stores</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-extrabold text-slate-900 font-heading">99.99%</div>
                            <div class="text-xs uppercase font-bold tracking-wider text-slate-400">Server Uptime</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-extrabold text-slate-900 font-heading">&lt; 150ms</div>
                            <div class="text-xs uppercase font-bold tracking-wider text-slate-400">Response Speed</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl font-extrabold text-slate-900 font-heading">100%</div>
                            <div class="text-xs uppercase font-bold tracking-wider text-slate-400">Data Isolation</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Grid Section -->
            <section id="features" class="py-24 bg-white relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <div class="text-center max-w-3xl mx-auto space-y-4 mb-20">
                        <h2 class="text-xs uppercase tracking-widest font-extrabold text-[#004c3f]">Fully Featured</h2>
                        <p class="text-3xl sm:text-4xl font-extrabold font-heading text-slate-900 tracking-tight">
                            Everything you need to succeed in e-commerce
                        </p>
                        <p class="text-slate-500">
                            Our multi-tenant SaaS infrastructure allows you to build custom storefronts with total isolation and high security.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        
                        <!-- Feature 1 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-100 hover:border-slate-200/80 p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-shopify-green flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 font-heading">Tenant Isolation</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Every merchant gets their own dedicated database instance. Complete data security, isolation, and privacy.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-100 hover:border-slate-200/80 p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1-1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 3.42-3.42m-3.42 3.42a15.997 15.997 0 0 1-3.388-1.62m0 0a15.996 15.996 0 0 1-1.622-3.395m3.42 3.42a15.996 15.996 0 0 0 3.42-3.42m0 0a15.998 15.998 0 0 0 1.622-3.395m-3.42 3.42a15.998 15.998 0 0 1-3.42-3.42m0 0A15.997 15.997 0 0 1 12 2.25a15.996 15.996 0 0 1 3.388 1.62m-5.008-.025a15.998 15.998 0 0 1 1.622 3.395m-3.42-3.42a15.997 15.997 0 0 0-3.42 3.42m3.42-3.42a15.998 15.998 0 0 1 3.388 1.62m0 0a15.998 15.998 0 0 1 1.622 3.395m-3.42-3.42a15.998 15.998 0 0 0-3.42 3.42m3.42-3.42a15.997 15.997 0 0 0 3.42 3.42m0 0c.263.585.51 1.186.74 1.792m-2.362-1.792a15.999 15.999 0 0 0-1.776 2.378m-2.457-4.17c-.584.263-1.185.51-1.79.74m1.79-.74a15.997 15.997 0 0 0-2.379 1.777M19.5 12a7.5 7.5 0 0 1-13.5 4.5" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 font-heading">Store Customizer</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Experience visual workspace editing with real-time responsive browser preview frame, PERSISTENT state, and RTL support.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-100 hover:border-slate-200/80 p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.5a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 font-heading">Catalog & Layouts</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Easily manage items, categorizations, inventories, pricing schemes, and layout templates with responsive controls.
                            </p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-100 hover:border-slate-200/80 p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2 font-heading">High Performance</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">
                                Powered by Laravel 12 core for incredibly fast page routing and optimized database reads on standard hardware.
                            </p>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Customizer Demo Preview Section -->
            <section id="customizer" class="py-24 bg-slate-50 border-y border-slate-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        
                        <!-- Left Mockup Preview Screen -->
                        <div class="lg:col-span-7 space-y-4">
                            <div class="rounded-3xl bg-white border border-slate-200 shadow-2xl p-4 overflow-hidden">
                                <!-- Top Bar -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3.5 h-3.5 rounded-full bg-slate-100 flex items-center justify-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        </span>
                                        <span class="text-xs font-bold text-slate-800">Visual Workspace Customizer</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="px-3 py-1 rounded bg-slate-100 text-[10px] font-bold text-slate-500">Desktop</div>
                                        <div class="px-3 py-1 rounded bg-shopify-green text-[10px] font-bold text-white">Mobile Mockup</div>
                                    </div>
                                </div>

                                <!-- Customizer Split Layout Graphic -->
                                <div class="grid grid-cols-12 gap-4 h-96">
                                    <!-- Config Column -->
                                    <div class="col-span-5 bg-slate-50 rounded-2xl border border-slate-100 p-3 flex flex-col gap-3 overflow-hidden">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold text-slate-700">Theme Editor</span>
                                            <span class="text-[8px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded font-bold">RTL Supported</span>
                                        </div>
                                        <div class="space-y-2 text-[9px]">
                                            <div class="p-2 rounded bg-white border border-slate-100">
                                                <div class="font-bold text-slate-700 mb-1">Announcement Text</div>
                                                <div class="bg-slate-50 p-1 border border-slate-100 rounded text-slate-500 font-mono">Special Winter Sale 20%</div>
                                            </div>
                                            <div class="p-2 rounded bg-white border border-slate-100">
                                                <div class="font-bold text-slate-700 mb-1">Theme Colors</div>
                                                <div class="flex gap-1">
                                                    <div class="w-4 h-4 rounded-full bg-[#004c3f] border border-slate-200"></div>
                                                    <div class="w-4 h-4 rounded-full bg-[#10b981] border border-slate-200"></div>
                                                    <div class="w-4 h-4 rounded-full bg-slate-900 border border-slate-200"></div>
                                                </div>
                                            </div>
                                            <div class="p-2 rounded bg-white border border-slate-100 flex items-center justify-between">
                                                <span class="font-bold text-slate-700">Enable RTL Mode</span>
                                                <span class="w-6 h-3 bg-emerald-500 rounded-full flex items-center justify-end px-0.5"><span class="w-2.5 h-2.5 bg-white rounded-full"></span></span>
                                            </div>
                                        </div>
                                        <div class="mt-auto p-2 bg-emerald-50 text-emerald-800 text-[8px] font-semibold rounded-lg border border-emerald-100 text-center">
                                            ✔ All settings persist on reload
                                        </div>
                                    </div>

                                    <!-- Phone Preview Column -->
                                    <div class="col-span-7 flex justify-center items-center bg-slate-950 rounded-2xl border border-slate-900 p-2">
                                        <div class="w-48 h-full bg-white rounded-xl overflow-hidden border border-slate-800 flex flex-col relative scale-95 origin-center">
                                            <!-- Top Notch -->
                                            <div class="w-full bg-slate-900 text-white text-[4px] py-0.5 flex justify-center gap-4 px-2 select-none">
                                                <span>9:41</span>
                                                <span class="w-8 h-1 bg-slate-800 rounded-full self-center"></span>
                                                <span>100%</span>
                                            </div>
                                            
                                            <!-- Store header in RTL preview -->
                                            <div class="bg-shopify-green text-white text-[4px] py-1 text-center font-bold">
                                                🚀 سردیوں کی سیل: 20 فیصد کی چھوٹ!
                                            </div>
                                            <div class="p-2 flex flex-col gap-2 overflow-y-auto">
                                                <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                                                    <div class="text-[7px] font-bold text-slate-900">میرا اسٹور</div>
                                                    <div class="flex gap-2 text-[5px] text-slate-400">
                                                        <span>صفحہ اول</span>
                                                        <span>پروڈکٹس</span>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 border border-slate-100 rounded-md p-2 flex flex-col gap-1 items-center text-center">
                                                    <span class="text-[7px] font-bold leading-tight">جدید اسٹورفرنٹ اب لائیو ہے</span>
                                                    <span class="text-[4px] text-slate-400">ہمارے پلیٹ فارم سے اپنا اسٹور بنائیں۔</span>
                                                    <span class="px-2 py-0.5 bg-slate-900 text-white rounded-[2px] text-[3px] font-bold">ابھی خریدیں</span>
                                                </div>
                                                <!-- Dynamic sales popup -->
                                                <div class="absolute bottom-2 left-2 right-2 bg-white/95 backdrop-blur-sm p-1.5 rounded border border-slate-100 shadow flex items-center gap-1.5 animate-pulse">
                                                    <div class="w-4 h-4 rounded bg-emerald-100 text-emerald-800 flex items-center justify-center text-[4px]">✔</div>
                                                    <div class="text-[3px] text-slate-600 leading-none">فہد نے کراچی سے لیدر بیگ خریدا!</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Info Text -->
                        <div class="lg:col-span-5 space-y-6 lg:pl-6">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold tracking-wide">
                                Live Store Editor
                            </div>
                            <h3 class="text-3xl font-extrabold text-slate-900 font-heading leading-tight">
                                Design your store visually in real time
                            </h3>
                            <p class="text-slate-500">
                                No coding required. Our advanced sidebar settings editor controls custom banner headers, announcement banners, color schemes, custom layouts, and footer modules.
                            </p>
                            
                            <ul class="space-y-3 font-semibold text-slate-700">
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Split-screen layout with instant device preview
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Persistent tab and accordion states on reload
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    RTL Layout switch (Nastaleeq typography support)
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Dynamic interactive sales notifications popups
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Pricing Plans Section -->
            <section id="pricing" class="py-24 bg-white" x-data="{ billingCycle: 'monthly' }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                        <h2 class="text-xs uppercase tracking-widest font-extrabold text-[#004c3f]">Pricing Plans</h2>
                        <p class="text-3xl sm:text-4xl font-extrabold font-heading text-slate-900 tracking-tight">
                            Simple, transparent pricing for any stage
                        </p>
                        <p class="text-slate-500">
                            Choose the plan that fits your business needs. Upgrade, downgrade, or cancel anytime.
                        </p>

                        <!-- Billing Switcher -->
                        <div class="inline-flex p-1.5 rounded-2xl bg-slate-100 border border-slate-200 mt-6 select-none">
                            <button type="button" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
                                    :class="billingCycle === 'monthly' ? 'bg-shopify-green text-white shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="billingCycle = 'monthly'">
                                Monthly
                            </button>
                            <button type="button" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all relative"
                                    :class="billingCycle === 'annual' ? 'bg-shopify-green text-white shadow' : 'text-slate-600 hover:text-slate-900'"
                                    @click="billingCycle = 'annual'">
                                Yearly Billed
                                <span class="absolute -top-3 -right-3 px-2 py-0.5 text-[8px] font-extrabold tracking-wide uppercase bg-emerald-500 text-white rounded-full animate-pulse">Save 20%</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto">
                        
                        <!-- Plan 1 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-300">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-900 font-heading">Starter Store</h3>
                                    <p class="text-xs text-slate-400 mt-1">Perfect for trying ideas out</p>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-slate-900 font-heading" x-text="billingCycle === 'monthly' ? '$9' : '$7'">$9</span>
                                    <span class="text-sm text-slate-500 font-medium">/mo</span>
                                </div>
                                <hr class="border-slate-200/80">
                                <ul class="space-y-4 text-sm font-medium text-slate-600">
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        1 User Storefront
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Up to 50 Products
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Theme Customizer (RTL Mode)
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Subdomain Hosting
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="block text-center w-full px-6 py-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-sm transition-colors">
                                    Start Free Trial
                                </a>
                            </div>
                        </div>

                        <!-- Plan 2 -->
                        <div class="bg-white border-2 border-shopify-green rounded-3xl p-8 flex flex-col justify-between shadow-xl shadow-slate-100 hover:shadow-2xl relative transition-all duration-300">
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-shopify-green text-white text-[10px] font-extrabold tracking-wider uppercase shadow">
                                Most Popular Plan
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-900 font-heading">Growth Shop</h3>
                                    <p class="text-xs text-shopify-green font-bold mt-1">For growing storefront brands</p>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-slate-900 font-heading" x-text="billingCycle === 'monthly' ? '$29' : '$23'">$29</span>
                                    <span class="text-sm text-slate-500 font-medium">/mo</span>
                                </div>
                                <hr class="border-slate-200">
                                <ul class="space-y-4 text-sm font-medium text-slate-600">
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        5 Staff Accounts
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Unlimited Products
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Premium Visual Customizer
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Custom Domains Config
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Isolated DB Security
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="block text-center w-full px-6 py-3.5 rounded-xl bg-shopify-green hover:bg-[#00382f] text-white font-bold text-sm shadow-md shadow-emerald-950/10 hover:shadow-emerald-950/20 transition-all hover:-translate-y-0.5">
                                    Start Free Trial
                                </a>
                            </div>
                        </div>

                        <!-- Plan 3 -->
                        <div class="bg-slate-50/50 hover:bg-white border border-slate-200/60 rounded-3xl p-8 flex flex-col justify-between hover:shadow-xl hover:border-slate-300 transition-all duration-300">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-xl font-extrabold text-slate-900 font-heading">Enterprise Suite</h3>
                                    <p class="text-xs text-slate-400 mt-1">For large volume operations</p>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-slate-900 font-heading" x-text="billingCycle === 'monthly' ? '$79' : '$63'">$79</span>
                                    <span class="text-sm text-slate-500 font-medium">/mo</span>
                                </div>
                                <hr class="border-slate-200/80">
                                <ul class="space-y-4 text-sm font-medium text-slate-600">
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Unlimited Staff Accounts
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Unlimited Products
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Dedicated Theme Designer
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Custom Database Tuning
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Priority 24/7 Call Support
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="block text-center w-full px-6 py-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-sm transition-colors">
                                    Start Free Trial
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- FAQs Accordion Section -->
            <section id="faqs" class="py-24 bg-slate-50 border-t border-slate-100" x-data="{ activeFaq: null }">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <div class="text-center space-y-4 mb-16">
                        <h2 class="text-xs uppercase tracking-widest font-extrabold text-[#004c3f]">Common Questions</h2>
                        <p class="text-3xl font-extrabold font-heading text-slate-900 tracking-tight">
                            Frequently Asked Questions
                        </p>
                        <p class="text-slate-500">
                            Got questions about our multi-tenant SaaS storefront platform? We have answers.
                        </p>
                    </div>

                    <div class="space-y-4">
                        
                        <!-- FAQ 1 -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <button type="button" class="w-full px-6 py-5 text-left font-bold text-slate-800 flex items-center justify-between text-base"
                                    @click="activeFaq === 1 ? activeFaq = null : activeFaq = 1">
                                <span>Is my store's data safe from other tenants?</span>
                                <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="activeFaq === 1 ? 'rotate-180 text-shopify-green' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="px-6 pb-6 text-sm text-slate-500 leading-relaxed" x-show="activeFaq === 1" x-transition style="display: none;">
                                Yes, absolutely. We use a multi-database architecture (via our Tenancy package) which generates separate isolated SQLite databases (`database/tenant<id>.sqlite`) for each store owner. Your products, customers, transactions, and session states are physically isolated.
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <button type="button" class="w-full px-6 py-5 text-left font-bold text-slate-800 flex items-center justify-between text-base"
                                    @click="activeFaq === 2 ? activeFaq = null : activeFaq = 2">
                                <span>Can I customize the storefront layout in real time?</span>
                                <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="activeFaq === 2 ? 'rotate-180 text-shopify-green' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="px-6 pb-6 text-sm text-slate-500 leading-relaxed" x-show="activeFaq === 2" x-transition style="display: none;">
                                Yes. Each merchant dashboard has access to a live **Store Customizer** with a responsive split screen layout. You can adjust theme styles, modify headers/footers, rearrange layout sections, enable RTL Arabic/Nastaleeq scripts, and instantly see changes inside the frame preview.
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <button type="button" class="w-full px-6 py-5 text-left font-bold text-slate-800 flex items-center justify-between text-base"
                                    @click="activeFaq === 3 ? activeFaq = null : activeFaq = 3">
                                <span>How do sales popups work on my storefront?</span>
                                <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="activeFaq === 3 ? 'rotate-180 text-shopify-green' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="px-6 pb-6 text-sm text-slate-500 leading-relaxed" x-show="activeFaq === 3" x-transition style="display: none;">
                                You can configure a mock sales notification popup list inside your theme settings (e.g. `Fahad | Karachi | Bag | 5m ago`). The storefront parses this input and displays elegant purchase banners periodically to boost customer trust and conversion.
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <button type="button" class="w-full px-6 py-5 text-left font-bold text-slate-800 flex items-center justify-between text-base"
                                    @click="activeFaq === 4 ? activeFaq = null : activeFaq = 4">
                                <span>Can I cancel my account at any time?</span>
                                <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="activeFaq === 4 ? 'rotate-180 text-shopify-green' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="px-6 pb-6 text-sm text-slate-500 leading-relaxed" x-show="activeFaq === 4" x-transition style="display: none;">
                                Yes. The platform supports monthly subscriptions without binding contracts. You can upgrade, downgrade, or cancel your merchant store subscription inside the portal.
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Final CTA Banner -->
            <section class="bg-shopify-green text-white py-20 relative overflow-hidden">
                <!-- Decorative Circle -->
                <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-emerald-800/40 blur-2xl"></div>
                <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-emerald-950/60 blur-2xl"></div>

                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative">
                    <h2 class="text-3xl sm:text-5xl font-extrabold font-heading tracking-tight max-w-3xl mx-auto leading-tight">
                        Grow your e-commerce dream brand today
                    </h2>
                    <p class="text-emerald-100 max-w-xl mx-auto text-base sm:text-lg">
                        Get all the power of multi-tenant store design, visual customizers, and product catalogs in one platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('register') }}" class="bg-white hover:bg-slate-50 text-[#004c3f] font-extrabold px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                            Start Your Free Trial
                        </a>
                        <a href="#features" class="text-white hover:text-emerald-200 font-bold px-6 py-4 flex items-center gap-1.5 transition-colors">
                            Explore platform features
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 pt-16 pb-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
                    
                    <!-- Logo / Brand Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <a href="/" class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl bg-shopify-green flex items-center justify-center text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold font-heading text-white tracking-tight">{{ config('app.name', 'ShopTenancy') }}</span>
                        </a>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-sm">
                            ShopTenancy is a modern multi-tenant SaaS e-commerce framework that gives you full power to launch isolated high-converting stores instantly.
                        </p>
                        <!-- Social Icons -->
                        <div class="flex gap-4">
                            <a href="#" class="hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6c.9 0 1.8.07 1.8.07v2h-1c-.96 0-1.3.6-1.3 1.2V12h2.2l-.35 3H13v6.8c4.56-.93 8-4.96 8-9.8z"/></svg>
                            </a>
                            <a href="#" class="hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.31 22.42c-5.52 0-10-4.48-10-10s4.48-10 10-10 10 4.48 10 10-4.48 10-10 10zm-1.1-12.72v5.3h2.2v-5.3h2.2v-2.2h-6.6v2.2h2.2z"/></svg>
                            </a>
                            <a href="#" class="hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Sitemap Column 1 -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Product</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                            <li><a href="#customizer" class="hover:text-white transition-colors">Live Customizer</a></li>
                            <li><a href="#pricing" class="hover:text-white transition-colors">Pricing Options</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">API Docs</a></li>
                        </ul>
                    </div>

                    <!-- Sitemap Column 2 -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Solutions</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Merchant Stores</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">RTL Multi-Lingual</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Multi-Tenant DBs</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Visual Editor</a></li>
                        </ul>
                    </div>

                    <!-- Sitemap Column 3 -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Support</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#faqs" class="hover:text-white transition-colors">FAQs Help</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contact Support</a></li>
                        </ul>
                    </div>

                </div>

                <hr class="border-slate-800 my-8">

                <!-- Bottom Footer Area -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                    <div>
                        © {{ date('Y') }} {{ config('app.name', 'ShopTenancy') }} Inc. All rights reserved.
                    </div>
                    <div>
                        Built with ❤️ on Laravel 12 & Tailwind CSS v4.
                    </div>
                </div>

            </div>
        </footer>

    </body>
</html>
