<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                
                {{-- 1. LOGO WMS PRO --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl tracking-tight">
                        <span class="text-cyan-400">WMS</span>PRO
                    </a>
                </div>

                {{-- 2. NAVIGATION LINKS (MENU UTAMA) --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- Dashboard Link --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 
                            {{ request()->routeIs('dashboard') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- MENU KHUSUS ADMIN: KELOLA PENGGUNA --}}
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" 
                            class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 
                            {{ request()->routeIs('users.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Pengguna') }}
                        </x-nav-link>
                    @endif

                    {{-- Transaksi (Bukan Supplier) --}}
                    @if(!Auth::user()->isSupplier())
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('transactions.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Transaksi') }}
                        </x-nav-link>
                    @endif

                    {{-- Master Data & Laporan (Admin & Manager) --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('products.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Produk') }}
                        </x-nav-link>
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('categories.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Kategori') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('reports.inventory')" :active="request()->routeIs('reports.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('reports.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Laporan') }}
                        </x-nav-link>
                    @endif

                    {{-- Restock Manager --}}
                    @if(Auth::user()->isManager())
                        <x-nav-link :href="route('restock_orders.index')" :active="request()->routeIs('restock_orders.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('restock_orders.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Restock') }}
                        </x-nav-link>
                    @endif

                    {{-- KHUSUS SUPPLIER: MENU "SEMUA PESANAN" --}}
                    @if(Auth::user()->isSupplier())
                        <x-nav-link :href="route('restock_orders.index')" :active="request()->routeIs('restock_orders.*')" class="text-gray-400 hover:text-cyan-400 focus:text-cyan-400 {{ request()->routeIs('restock_orders.*') ? '!text-white !border-cyan-500' : '!border-transparent' }}">
                            {{ __('Semua Pesanan') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- 3. SUDUT KANAN: SEARCH, NOTIF, PROFILE --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                
                {{-- GLOBAL SEARCH BAR --}}
                <form action="#" method="GET" class="relative">
                    <input type="text" name="query" placeholder="Cari..." 
                           class="w-48 px-3 py-2 border border-gray-700 rounded-lg text-sm bg-gray-800 text-white focus:border-cyan-500 focus:ring-cyan-500 transition">
                    <button type="button" class="absolute right-0 top-0 mt-2 mr-3 text-gray-400 hover:text-cyan-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>

                {{-- NOTIFIKASI --}}
                <button class="p-2 text-gray-400 hover:text-cyan-400 relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500 border border-gray-900"></span>
                </button>

                {{-- PROFILE DROPDOWN --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-300 hover:text-white transition">
                            <div class="h-9 w-9 bg-cyan-400 rounded-full flex items-center justify-center text-gray-900 font-bold text-base mr-2 shadow-md">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="text-left hidden lg:block">
                                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                            <svg class="fill-current h-4 w-4 ms-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
                            <x-dropdown-link :href="route('profile.edit')" class="text-gray-300 hover:bg-gray-700 hover:text-cyan-400">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="text-red-400 hover:bg-gray-700">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    {{-- Responsive Navigation Menu (Mobile) --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800 border-t border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- Mobile Link: Users (Admin Only) --}}
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Kelola Pengguna') }}
                </x-responsive-nav-link>
            @endif

            @if(!Auth::user()->isSupplier())
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Transaksi') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Produk') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.inventory')" :active="request()->routeIs('reports.*')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Laporan') }}
                </x-responsive-nav-link>
            @endif

            {{-- Restock Mobile --}}
            @if(Auth::user()->isManager() || Auth::user()->isSupplier())
                <x-responsive-nav-link :href="route('restock_orders.index')" :active="request()->routeIs('restock_orders.*')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Semua Pesanan') }}
                </x-responsive-nav-link>
            @endif
        </div>

        {{-- Mobile Settings Options --}}
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-white hover:bg-gray-700">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-400 hover:text-red-300 hover:bg-gray-700">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>