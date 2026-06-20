<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 rounded-lg bg-[#004c3f] flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                        <span class="text-lg font-extrabold tracking-tight text-slate-900" style="font-family: 'Outfit', sans-serif;">{{ config('app.name', 'ShopTenancy') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center space-x-1 h-full pt-1">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-bold rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-[#004c3f]/5 text-[#004c3f]' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                            <span class="w-6 h-6 rounded-full bg-[#004c3f] text-white flex items-center justify-center text-[10px] font-black uppercase">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </span>
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-50 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-slate-100">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-xl text-base font-bold transition {{ request()->routeIs('dashboard') ? 'bg-[#004c3f]/5 text-[#004c3f]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-slate-100">
            <div class="px-6 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-[#004c3f] text-white flex items-center justify-center text-xs font-black uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <div>
                    <div class="font-bold text-slate-800 text-sm leading-none">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-slate-400 mt-1">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1.5 px-4">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    {{ __('Profile') }}
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="block px-4 py-2 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
