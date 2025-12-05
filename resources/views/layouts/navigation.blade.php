<nav x-data="{ open: false }" class="bg-slate-900 border-b border-slate-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl tracking-tight">
                        {{-- Ganti Logo SVG dengan Teks/Logo Putih --}}
                        <span class="text-blue-500">WMS</span>PRO
                    </a>
                </div>

                <!-- Navigation Links (Desktop Menu) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    {{-- Kita perlu custom class untuk Nav Link agar support dark mode navbar --}}
                    {{-- Active: text-white border-blue-500 --}}
                    {{-- Inactive: text-slate-400 hover:text-slate-200 --}}

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-300 hover:text-white focus:text-white {{ request()->routeIs('dashboard') ? '!text-white !border-blue-500' : '!border-transparent' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(!Auth::user()->isSupplier())
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="text-slate-300 hover:text-white focus:text-white {{ request()->routeIs('transactions.*') ? '!text-white !border-blue-500' : '!border-transparent' }}">
                            {{ __('Transaksi') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-slate-300 hover:text-white focus:text-white {{ request()->routeIs('products.*') ? '!text-white !border-blue-500' : '!border-transparent' }}">
                            {{ __('Produk') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-slate-300 hover:text-white focus:text-white {{ request()->routeIs('categories.*') ? '!text-white !border-blue-500' : '!border-transparent' }}">
                            {{ __('Kategori') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isAdmin() || Auth::user()->isManager() || Auth::user()->isSupplier())
                        <x-nav-link :href="route('restock_orders.index')" :active="request()->routeIs('restock_orders.*')" class="text-slate-300 hover:text-white focus:text-white {{ request()->routeIs('restock_orders.*') ? '!text-white !border-blue-500' : '!border-transparent' }}">
                            {{ __('Restock') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-300 bg-slate-800 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} <span class="text-xs text-slate-500">({{ ucfirst(Auth::user()->role) }})</span></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Menu Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-800 border-t border-slate-700">
        <div class="pt-2 pb-3 space-y-1">
            {{-- Mobile links perlu disesuaikan warnanya via CSS default atau override di sini --}}
            {{-- Untuk simplifikasi, kita biarkan default dulu atau tambahkan class text-slate-300 --}}
            
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(!Auth::user()->isSupplier())
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                    {{ __('Transaksi') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                    {{ __('Produk & Stok') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                    {{ __('Kategori') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->isAdmin() || Auth::user()->isManager() || Auth::user()->isSupplier())
                <x-responsive-nav-link :href="route('restock_orders.index')" :active="request()->routeIs('restock_orders.*')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                    {{ __('Restock Order') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-slate-300 hover:text-white hover:bg-slate-700">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-slate-300 hover:text-white hover:bg-slate-700">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>