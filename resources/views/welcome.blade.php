<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SaaS Commerce') }} - Start & Grow Your Online Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                    },
                },
            },
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-white text-gray-800 antialiased font-sans"
      x-data="{
          mobileMenu: false,
          heroWord: 0,
          heroWords: ['Build your store', 'Sell everywhere', 'Grow with WhatsApp', 'Scale globally'],
          billing: 'monthly',
          openFaq: null
      }"
      x-init="setInterval(() => { heroWord = (heroWord + 1) % heroWords.length }, 3000)">

    {{-- 1. Announcement Bar --}}
    <div class="bg-brand-900 text-white text-center py-2.5 px-4 text-xs sm:text-sm font-medium tracking-wide">
        Start your store today &mdash; 14-day free trial. No credit card required.
    </div>

    {{-- 2. Sticky Navigation --}}
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100"
            x-data="{ scrolled: false }"
            @scroll.window="scrolled = (window.scrollY > 10)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-lg bg-brand-800 flex items-center justify-center text-white group-hover:bg-brand-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'SaaS Commerce') }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#features" class="hover:text-gray-900 transition-colors">Features</a>
                <a href="#sell-everywhere" class="hover:text-gray-900 transition-colors">Sell Everywhere</a>
                <a href="#pricing" class="hover:text-gray-900 transition-colors">Pricing</a>
                <a href="#faqs" class="hover:text-gray-900 transition-colors">FAQs</a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900 px-3 py-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900 px-3 py-2">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-brand-800 hover:bg-brand-700 px-5 py-2.5 rounded-lg transition-colors">Start free trial</a>
                        @endif
                    @endauth
                @endif
            </div>

            <button type="button" class="md:hidden p-2 text-gray-600 hover:text-gray-900" @click="mobileMenu = !mobileMenu">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-4 space-y-2">
                <a href="#features" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" @click="mobileMenu = false">Features</a>
                <a href="#sell-everywhere" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" @click="mobileMenu = false">Sell Everywhere</a>
                <a href="#pricing" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" @click="mobileMenu = false">Pricing</a>
                <a href="#faqs" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" @click="mobileMenu = false">FAQs</a>
                <hr class="border-gray-100">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block text-center px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700" @click="mobileMenu = false">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block text-center px-4 py-2.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700" @click="mobileMenu = false">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block text-center px-4 py-2.5 rounded-lg bg-brand-800 text-sm font-semibold text-white" @click="mobileMenu = false">Start free trial</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main>

        {{-- 3. Hero Section --}}
        <section class="relative overflow-hidden bg-gradient-to-b from-gray-50 to-white pt-16 pb-20 lg:pt-24 lg:pb-28">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-100/40 rounded-full blur-3xl -z-10 -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-gray-100 rounded-full blur-2xl -z-10 translate-y-1/2 -translate-x-1/3"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    <div class="space-y-8 text-center lg:text-left">
                        <div>
                            <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-4">Multi-tenant SaaS Platform</p>
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
                                Be the next
                                <span class="block text-brand-800 mt-1 h-[1.2em] overflow-hidden relative">
                                    <template x-for="(word, i) in heroWords" :key="i">
                                        <span class="absolute inset-0 transition-all duration-500 ease-in-out"
                                              :class="heroWord === i ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-full'">
                                            <span x-text="word"></span>
                                        </span>
                                    </template>
                                </span>
                            </h1>
                        </div>

                        <p class="text-lg text-gray-500 max-w-lg mx-auto lg:mx-0">
                            Launch a fully-functional e-commerce store in minutes. Manage products, accept payments, and grow your brand with WhatsApp integration.
                        </p>

                        @auth
                            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-brand-800 hover:bg-brand-700 text-white font-bold text-base shadow-lg shadow-brand-900/20 transition-all hover:-translate-y-0.5">
                                    Go to Dashboard
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                </a>
                            </div>
                        @else
                            <form action="{{ route('register') }}" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto lg:mx-0">
                                <input type="email" name="email" placeholder="Enter your email address" required
                                       class="flex-1 px-5 py-3.5 rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent shadow-sm">
                                <button type="submit" class="px-7 py-3.5 rounded-xl bg-brand-800 hover:bg-brand-700 text-white font-bold text-sm whitespace-nowrap shadow-lg shadow-brand-900/20 transition-all hover:-translate-y-0.5">
                                    Start free trial
                                </button>
                            </form>
                            <p class="text-xs text-gray-400 text-center lg:text-left">Try free for 14 days. No setup fees. Cancel anytime.</p>
                        @endauth
                    </div>

                    <div class="relative flex justify-center">
                        <div class="w-full max-w-lg">
                            <div class="rounded-2xl bg-gray-900 shadow-2xl shadow-gray-900/30 border border-gray-800 p-3 overflow-hidden">
                                <div class="flex items-center gap-2 pb-3 px-1 border-b border-gray-800">
                                    <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                                    <div class="flex-1 mx-4 bg-gray-800 rounded-lg text-[10px] font-medium text-gray-400 py-1.5 px-4 text-center flex items-center justify-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3 h-3 text-gray-500"><path fill-rule="evenodd" d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7V4.5A3.5 3.5 0 0 0 8 1Zm2 6V4.5a2 2 0 1 0-4 0V7h4Z" clip-rule="evenodd"/></svg>
                                        yourstore.pk
                                    </div>
                                    <div class="w-9"></div>
                                </div>

                                <div class="bg-white rounded-xl mt-3 p-4 space-y-3">
                                    <div class="bg-brand-800 text-white text-[9px] text-center py-1 rounded font-semibold">Free Shipping on Orders Over PKR 2,000!</div>
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                                        <span class="text-xs font-bold text-gray-900">YourStore</span>
                                        <div class="flex gap-3 text-[10px] text-gray-500 font-medium">
                                            <span>Home</span><span>Catalog</span><span>About</span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 text-center space-y-2">
                                        <div class="text-sm font-extrabold text-gray-900">Summer Collection</div>
                                        <div class="text-[10px] text-gray-400">Premium products curated for you.</div>
                                        <span class="inline-block px-3 py-1 bg-gray-900 text-white rounded text-[9px] font-semibold">Shop Now</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="bg-gray-50 border border-gray-100 rounded p-1.5 flex flex-col gap-1">
                                            <div class="aspect-square bg-gray-200 rounded-sm"></div>
                                            <div class="h-1 w-8 bg-gray-300 rounded-full"></div>
                                            <div class="h-1 w-5 bg-gray-900 rounded-full"></div>
                                        </div>
                                        <div class="bg-gray-50 border border-gray-100 rounded p-1.5 flex flex-col gap-1">
                                            <div class="aspect-square bg-gray-200 rounded-sm"></div>
                                            <div class="h-1 w-8 bg-gray-300 rounded-full"></div>
                                            <div class="h-1 w-5 bg-gray-900 rounded-full"></div>
                                        </div>
                                        <div class="bg-gray-50 border border-gray-100 rounded p-1.5 flex flex-col gap-1">
                                            <div class="aspect-square bg-gray-200 rounded-sm"></div>
                                            <div class="h-1 w-8 bg-gray-300 rounded-full"></div>
                                            <div class="h-1 w-5 bg-gray-900 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="absolute -left-4 top-1/4 bg-white p-3 rounded-xl shadow-xl shadow-gray-200/60 border border-gray-100 flex items-center gap-2.5 animate-bounce" style="animation-duration:6s">
                                <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/></svg>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Total Sales</div>
                                    <div class="text-sm font-extrabold text-gray-900">PKR 2.4M</div>
                                </div>
                            </div>

                            <div class="absolute -right-4 bottom-1/4 bg-white p-3 rounded-xl shadow-xl shadow-gray-200/60 border border-gray-100 flex items-center gap-2.5 animate-bounce" style="animation-duration:8s;animation-delay:1s">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                                </div>
                                <div>
                                    <div class="text-[9px] font-bold uppercase tracking-wider text-gray-400">New Customers</div>
                                    <div class="text-sm font-extrabold text-gray-900">+342 Today</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. Social Proof Bar --}}
        <section class="border-y border-gray-100 bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-extrabold text-gray-900">15,000+</div>
                        <div class="text-xs uppercase font-bold tracking-wider text-gray-400 mt-1">Active Stores</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-gray-900">99.9%</div>
                        <div class="text-xs uppercase font-bold tracking-wider text-gray-400 mt-1">Uptime</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-gray-900">&lt;200ms</div>
                        <div class="text-xs uppercase font-bold tracking-wider text-gray-400 mt-1">Page Load</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-gray-900">PKR 2B+</div>
                        <div class="text-xs uppercase font-bold tracking-wider text-gray-400 mt-1">Orders Processed</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Sell Everywhere --}}
        <section id="sell-everywhere" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-3">Multi-Channel Selling</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Sell everywhere your customers are</h2>
                    <p class="text-gray-500 mt-4 text-lg">Reach more customers by selling through multiple channels from a single dashboard.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-7 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.5a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Online Store</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">A fully customizable storefront with your own domain, themes, and product catalog.</p>
                    </div>

                    <div class="p-7 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">WhatsApp CRM</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Send order updates, manage conversations, and sell directly through WhatsApp Business API.</p>
                    </div>

                    <div class="p-7 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Social Commerce</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Sync products to Instagram, Facebook, and TikTok shops with automatic inventory updates.</p>
                    </div>

                    <div class="p-7 rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">COD & Payments</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Accept Cash on Delivery, JazzCash, EasyPaisa, Stripe, and local bank transfers.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 6. WhatsApp CRM Feature --}}
        <section class="py-20 lg:py-28 bg-brand-900 text-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                    <div class="space-y-8">
                        <div>
                            <p class="text-sm font-semibold text-brand-300 tracking-wide uppercase mb-3">WhatsApp CRM</p>
                            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">Turn conversations into sales</h2>
                            <p class="text-brand-200 mt-4 text-lg">Automate order updates, manage customer conversations, and drive repeat purchases — all from WhatsApp.</p>
                        </div>

                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-brand-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-brand-100"><strong class="text-white">Auto Order Updates</strong> &mdash; Send shipping confirmations and delivery alerts automatically via WhatsApp.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-brand-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-brand-100"><strong class="text-white">Unified Chat Panel</strong> &mdash; Manage all customer conversations from a single integrated inbox.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-brand-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-brand-100"><strong class="text-white">Message Templates</strong> &mdash; Pre-built templates for order confirmations, promotions, and abandoned cart recovery.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-brand-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-brand-100"><strong class="text-white">Cloud API</strong> &mdash; Direct integration with WhatsApp Business Cloud API for reliable message delivery.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="relative">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/10 p-6 max-w-md mx-auto">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-full bg-brand-600 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold">WhatsApp Business</div>
                                    <div class="text-xs text-brand-300">Cloud API Connected</div>
                                </div>
                                <div class="ml-auto w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></div>
                            </div>

                            <div class="space-y-3">
                                <div class="bg-white/10 rounded-xl rounded-tl-sm p-3 max-w-[85%]">
                                    <p class="text-xs font-bold text-white mb-1">Order #1234 Shipped</p>
                                    <p class="text-[11px] text-brand-200">Hi Ahmed! Your order has been shipped via TCS. Tracking: TC123456789. Expected delivery: Tomorrow.</p>
                                    <span class="text-[9px] text-brand-400 mt-1 block text-right">10:30 AM</span>
                                </div>
                                <div class="bg-white/10 rounded-xl rounded-tl-sm p-3 max-w-[85%]">
                                    <p class="text-xs font-bold text-white mb-1">Payment Received</p>
                                    <p class="text-[11px] text-brand-200">PKR 4,500 received via JazzCash for Order #1240. Thank you for shopping with us!</p>
                                    <span class="text-[9px] text-brand-400 mt-1 block text-right">11:15 AM</span>
                                </div>
                                <div class="bg-brand-600 rounded-xl rounded-tr-sm p-3 max-w-[85%] ml-auto">
                                    <p class="text-[11px] text-white">Is this available in blue?</p>
                                    <span class="text-[9px] text-brand-300 mt-1 block text-right">11:20 AM</span>
                                </div>
                            </div>

                            <div class="mt-5 pt-4 border-t border-white/10 grid grid-cols-2 gap-3">
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <div class="text-lg font-extrabold text-white">1,247</div>
                                    <div class="text-[10px] text-brand-300 font-medium">Messages Sent</div>
                                </div>
                                <div class="bg-white/5 rounded-lg p-2.5 text-center">
                                    <div class="text-lg font-extrabold text-white">98.5%</div>
                                    <div class="text-[10px] text-brand-300 font-medium">Delivery Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 7. Features Grid --}}
        <section id="features" class="py-20 lg:py-28 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-3">Platform Features</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Everything you need to succeed</h2>
                    <p class="text-gray-500 mt-4 text-lg">A complete multi-tenant SaaS infrastructure built for scale, security, and speed.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Tenant Isolation</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Each merchant gets a dedicated database. Complete data security and privacy between stores.</p>
                    </div>

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1-1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 3.42-3.42m-3.42 3.42a15.997 15.997 0 0 1-3.388-1.62m0 0a15.996 15.996 0 0 1-1.622-3.395m3.42 3.42a15.996 15.996 0 0 0 3.42-3.42m0 0a15.998 15.998 0 0 0 1.622-3.395m-3.42 3.42a15.998 15.998 0 0 1-3.42-3.42m0 0A15.997 15.997 0 0 1 12 2.25a15.996 15.996 0 0 1 3.388 1.62m-5.008-.025a15.998 15.998 0 0 1 1.622 3.395m-3.42-3.42a15.997 15.997 0 0 0-3.42 3.42m3.42-3.42a15.998 15.998 0 0 1 3.388 1.62m0 0a15.998 15.998 0 0 1 1.622 3.395m-3.42-3.42a15.998 15.998 0 0 0-3.42 3.42m3.42-3.42a15.997 15.997 0 0 0 3.42 3.42m0 0c.263.585.51 1.186.74 1.792m-2.362-1.792a15.999 15.999 0 0 0-1.776 2.378m-2.457-4.17c-.584.263-1.185.51-1.79.74m1.79-.74a15.997 15.997 0 0 0-2.379 1.777M19.5 12a7.5 7.5 0 0 1-13.5 4.5"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Visual Store Customizer</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Design your storefront visually in real time with a split-screen editor, RTL support, and persistent state.</p>
                    </div>

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">WhatsApp CRM</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Built-in WhatsApp integration for order updates, customer support, and marketing campaigns.</p>
                    </div>

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">High Performance</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Optimized database reads and Laravel core routing for sub-200ms page loads on standard hardware.</p>
                    </div>

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Multi-Tenant Database</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Each store runs on its own isolated SQLite database with automatic provisioning and migration.</p>
                    </div>

                    <div class="bg-white rounded-2xl p-7 border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 group">
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15a2.25 2.25 0 0 1 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Order Management</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Track orders from placement to delivery with status updates, returns management, and analytics.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 8. Build Fast --}}
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-3">Get Started Fast</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Launch in three simple steps</h2>
                    <p class="text-gray-500 mt-4 text-lg">Go from zero to live store in under 10 minutes.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                    <div class="text-center space-y-5">
                        <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-800 flex items-center justify-center mx-auto text-2xl font-extrabold">1</div>
                        <h3 class="text-xl font-bold text-gray-900">Add your products</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Upload product images, set prices, write descriptions, and organize into collections. Bulk import supported.</p>
                    </div>

                    <div class="text-center space-y-5">
                        <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-800 flex items-center justify-center mx-auto text-2xl font-extrabold">2</div>
                        <h3 class="text-xl font-bold text-gray-900">Customize your store</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Use the visual customizer to pick colors, fonts, layouts, and banners. See changes live in real time.</p>
                    </div>

                    <div class="text-center space-y-5">
                        <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-800 flex items-center justify-center mx-auto text-2xl font-extrabold">3</div>
                        <h3 class="text-xl font-bold text-gray-900">Start selling</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Connect a payment method, enable WhatsApp notifications, and share your store link with customers.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 9. Pricing --}}
        <section id="pricing" class="py-20 lg:py-28 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-3">Pricing</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Simple, transparent pricing</h2>
                    <p class="text-gray-500 mt-4 text-lg">Choose the plan that fits your business. Upgrade, downgrade, or cancel anytime.</p>
                </div>

                <div class="flex justify-center mb-12">
                    <div class="inline-flex p-1 rounded-xl bg-gray-200/80">
                        <button type="button" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all"
                                :class="billing === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                @click="billing = 'monthly'">
                            Monthly
                        </button>
                        <button type="button" class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all relative"
                                :class="billing === 'annual' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                @click="billing = 'annual'">
                            Annual
                            <span class="absolute -top-3 -right-3 px-2 py-0.5 text-[9px] font-extrabold uppercase bg-green-500 text-white rounded-full">Save 20%</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto items-stretch">

                    <div class="bg-white rounded-2xl border border-gray-200 p-8 flex flex-col justify-between hover:shadow-lg transition-all">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-900">Starter</h3>
                                <p class="text-sm text-gray-400 mt-1">Perfect for trying ideas out</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900" x-text="billing === 'monthly' ? '$9' : '$7'">$9</span>
                                <span class="text-sm text-gray-500">/mo</span>
                            </div>
                            <hr class="border-gray-100">
                            <ul class="space-y-3.5 text-sm font-medium text-gray-600">
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>1 Staff Account</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Up to 50 Products</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Theme Customizer</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Subdomain Hosting</li>
                            </ul>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" class="block text-center w-full py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold text-sm transition-colors">Start Free Trial</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border-2 border-brand-700 p-8 flex flex-col justify-between shadow-xl relative">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-brand-800 text-white text-[10px] font-extrabold tracking-wider uppercase shadow">Most Popular</div>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-900">Growth</h3>
                                <p class="text-sm text-brand-700 font-semibold mt-1">For growing brands</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900" x-text="billing === 'monthly' ? '$29' : '$23'">$29</span>
                                <span class="text-sm text-gray-500">/mo</span>
                            </div>
                            <hr class="border-gray-100">
                            <ul class="space-y-3.5 text-sm font-medium text-gray-600">
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>5 Staff Accounts</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Unlimited Products</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Premium Customizer</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Custom Domain</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>WhatsApp CRM</li>
                            </ul>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" class="block text-center w-full py-3 rounded-xl bg-brand-800 hover:bg-brand-700 text-white font-bold text-sm shadow-lg shadow-brand-900/20 transition-all hover:-translate-y-0.5">Start Free Trial</a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-8 flex flex-col justify-between hover:shadow-lg transition-all">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-900">Enterprise</h3>
                                <p class="text-sm text-gray-400 mt-1">For large operations</p>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-gray-900" x-text="billing === 'monthly' ? '$79' : '$63'">$79</span>
                                <span class="text-sm text-gray-500">/mo</span>
                            </div>
                            <hr class="border-gray-100">
                            <ul class="space-y-3.5 text-sm font-medium text-gray-600">
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Unlimited Staff</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Unlimited Products</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Dedicated Theme Designer</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Database Tuning</li>
                                <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Priority 24/7 Support</li>
                            </ul>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('register') }}" class="block text-center w-full py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-bold text-sm transition-colors">Start Free Trial</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 10. FAQs --}}
        <section id="faqs" class="py-20 lg:py-28 bg-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <p class="text-sm font-semibold text-brand-700 tracking-wide uppercase mb-3">FAQs</p>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Frequently asked questions</h2>
                </div>

                <div class="space-y-3">

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" class="w-full px-6 py-4 text-left font-bold text-gray-900 flex items-center justify-between text-sm sm:text-base hover:bg-gray-50 transition-colors"
                                @click="openFaq === 1 ? openFaq = null : openFaq = 1">
                            <span>Is my store's data safe from other tenants?</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 ml-4 transition-transform duration-200" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFaq === 1" x-collapse>
                            <div class="px-6 pb-5 text-sm text-gray-500 leading-relaxed">
                                Yes, absolutely. Each merchant gets a dedicated isolated database. Your products, customers, orders, and session data are physically separated from all other tenants on the platform.
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" class="w-full px-6 py-4 text-left font-bold text-gray-900 flex items-center justify-between text-sm sm:text-base hover:bg-gray-50 transition-colors"
                                @click="openFaq === 2 ? openFaq = null : openFaq = 2">
                            <span>Can I customize my storefront in real time?</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 ml-4 transition-transform duration-200" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFaq === 2" x-collapse>
                            <div class="px-6 pb-5 text-sm text-gray-500 leading-relaxed">
                                Yes. The visual store customizer gives you a split-screen editor with live preview. You can change colors, fonts, banners, headers, footers, and layout sections. All changes persist on reload and support RTL layouts.
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" class="w-full px-6 py-4 text-left font-bold text-gray-900 flex items-center justify-between text-sm sm:text-base hover:bg-gray-50 transition-colors"
                                @click="openFaq === 3 ? openFaq = null : openFaq = 3">
                            <span>What payment methods do you support?</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 ml-4 transition-transform duration-200" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFaq === 3" x-collapse>
                            <div class="px-6 pb-5 text-sm text-gray-500 leading-relaxed">
                                We support Cash on Delivery (COD), JazzCash, EasyPaisa, Stripe, and direct bank transfers. You can enable multiple payment methods from your dashboard and customers will see all available options at checkout.
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" class="w-full px-6 py-4 text-left font-bold text-gray-900 flex items-center justify-between text-sm sm:text-base hover:bg-gray-50 transition-colors"
                                @click="openFaq === 4 ? openFaq = null : openFaq = 4">
                            <span>Can I cancel my subscription at any time?</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 ml-4 transition-transform duration-200" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="openFaq === 4" x-collapse>
                            <div class="px-6 pb-5 text-sm text-gray-500 leading-relaxed">
                                Yes. There are no binding contracts. You can upgrade, downgrade, or cancel your subscription anytime from your merchant dashboard. Your data remains accessible for 30 days after cancellation.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 11. Final CTA --}}
        <section class="py-20 lg:py-24 bg-brand-900 text-white relative overflow-hidden">
            <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-brand-800/50 blur-3xl"></div>
            <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-brand-950/60 blur-3xl"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Start selling online today
                </h2>
                <p class="text-brand-200 max-w-xl mx-auto text-lg">
                    Join 15,000+ merchants who built their stores on our platform. Free 14-day trial, no credit card required.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 rounded-xl bg-white text-brand-900 font-extrabold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                                Go to Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-white text-brand-900 font-extrabold shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                                    Start your free trial
                                </a>
                            @endif
                        @endauth
                    @endif
                    <a href="#features" class="px-8 py-4 rounded-xl border border-white/30 text-white font-bold hover:bg-white/10 transition-colors">
                        Explore features
                    </a>
                </div>
            </div>
        </section>

    </main>

    {{-- 12. Footer --}}
    <footer class="bg-gray-900 text-gray-400 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">

                <div class="lg:col-span-2 space-y-5">
                    <a href="/" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-brand-800 flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">{{ config('app.name', 'SaaS Commerce') }}</span>
                    </a>
                    <p class="text-sm leading-relaxed max-w-sm">
                        A modern multi-tenant SaaS e-commerce platform that gives you full power to launch isolated, high-converting online stores.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6c.9 0 1.8.07 1.8.07v2h-1c-.96 0-1.3.6-1.3 1.2V12h2.2l-.35 3H13v6.8c4.56-.93 8-4.96 8-9.8z"/></svg>
                        </a>
                        <a href="#" class="hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Product</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#sell-everywhere" class="hover:text-white transition-colors">Sell Everywhere</a></li>
                        <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API Docs</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Solutions</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Merchant Stores</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">WhatsApp CRM</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Multi-Tenant DBs</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Visual Editor</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">Support</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#faqs" class="hover:text-white transition-colors">FAQs</a></li>
                        <li><a href="{{ url('/privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ url('/terms-of-service') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact Support</a></li>
                    </ul>
                </div>
            </div>

            <hr class="border-gray-800">

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500 mt-8">
                <div>&copy; {{ date('Y') }} {{ config('app.name', 'SaaS Commerce') }}. All rights reserved.</div>
                <div>Built with Laravel &amp; Tailwind CSS</div>
            </div>
        </div>
    </footer>

</body>
</html>
