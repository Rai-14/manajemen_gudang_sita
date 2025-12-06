<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'WMSPRO') }} - Sistem Manajemen Gudang</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN (Jaminan Tampilan Rapi Instan) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        slate: {
                            850: '#1e293b',
                        }
                    },
                    animation: {
                        blob: "blob 7s infinite",
                    },
                    keyframes: {
                        blob: {
                            "0%": { transform: "translate(0px, 0px) scale(1)" },
                            "33%": { transform: "translate(30px, -50px) scale(1.1)" },
                            "66%": { transform: "translate(-20px, 20px) scale(0.9)" },
                            "100%": { transform: "translate(0px, 0px) scale(1)" },
                        },
                    },
                }
            }
        }
    </script>

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-600">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200 fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="#" class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <span>WMS<span class="text-blue-600">PRO</span></span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 px-4 py-2 transition">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">Register Supplier</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-slate-900 overflow-hidden min-h-screen flex items-center pt-16">
        <!-- Background Effects -->
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-900/20 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
                
                <!-- Text Content -->
                <div class="lg:w-1/2 text-center lg:text-left">
                    <div class="inline-flex items-center px-3 py-1 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-xs font-bold mb-6 uppercase tracking-widest animate-pulse">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                        Sistem Gudang Terintegrasi v1.0
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight leading-tight mb-6">
                        Kelola Stok <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-emerald-400">Tanpa Batas.</span>
                    </h1>
                    <p class="text-lg text-slate-400 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Platform manajemen inventori modern untuk mencatat barang masuk, keluar, dan restock order secara real-time. Dilengkapi fitur approval manager dan portal supplier.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition transform hover:-translate-y-1 text-center flex items-center justify-center gap-2">
                                Buka Dashboard
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('portal.akses') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition transform hover:-translate-y-1 text-center">
                                Login Staff / Manager
                            </a>
                            <a href="{{ route('portal.supplier') }}" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl border border-slate-700 transition text-center hover:border-slate-500">
                                Daftar Mitra Supplier
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Visual Content (Floating Cards) -->
                <div class="lg:w-1/2 relative w-full max-w-md lg:max-w-full">
                    <!-- Blob Animation -->
                    <div class="absolute -top-20 -right-20 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[100px] opacity-30 animate-blob"></div>
                    <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-cyan-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-30 animate-blob animation-delay-2000"></div>

                    <!-- Main Card -->
                    <div class="relative rounded-2xl bg-slate-800/80 border border-slate-700 p-6 shadow-2xl backdrop-blur-xl rotate-2 hover:rotate-0 transition duration-500">
                        <!-- Header Mockup -->
                        <div class="flex justify-between items-center mb-6 border-b border-slate-700 pb-4">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="text-xs font-mono text-slate-500">dashboard.blade.php</div>
                        </div>
                        
                        <!-- Content Mockup -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Stat Card 1 -->
                            <div class="bg-slate-700/50 p-4 rounded-xl border border-slate-600">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <div class="text-xs text-slate-400">Total Stok</div>
                                </div>
                                <div class="text-2xl font-bold text-white">1,240</div>
                            </div>
                            
                            <!-- Stat Card 2 -->
                            <div class="bg-slate-700/50 p-4 rounded-xl border border-slate-600">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="text-xs text-slate-400">Approved</div>
                                </div>
                                <div class="text-2xl font-bold text-white">85%</div>
                            </div>

                            <!-- Chart Mockup -->
                            <div class="col-span-2 bg-slate-700/30 p-4 rounded-xl border border-slate-600 h-32 flex items-end gap-2 justify-between px-6">
                                <div class="w-full bg-blue-600/40 rounded-t hover:bg-blue-500 transition h-[40%]"></div>
                                <div class="w-full bg-blue-600/40 rounded-t hover:bg-blue-500 transition h-[70%]"></div>
                                <div class="w-full bg-blue-500 rounded-t shadow-[0_0_15px_rgba(59,130,246,0.5)] h-[100%] relative group">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-white text-slate-900 text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition">High</div>
                                </div>
                                <div class="w-full bg-blue-600/40 rounded-t hover:bg-blue-500 transition h-[60%]"></div>
                                <div class="w-full bg-blue-600/40 rounded-t hover:bg-blue-500 transition h-[50%]"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge -->
                    <div class="absolute -right-6 top-20 bg-white text-slate-900 p-3 rounded-lg shadow-xl flex items-center gap-3 animate-bounce duration-[3000ms]">
                        <div class="bg-green-100 p-2 rounded-full text-green-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">Restock Diterima</div>
                            <div class="text-[10px] text-slate-500">Baru saja</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-slate-900">Fitur Unggulan</h2>
                <p class="mt-4 text-slate-500">Didesain untuk efisiensi operasional gudang modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Feature 1 -->
                <div class="group p-8 rounded-2xl bg-slate-50 hover:bg-blue-50 transition duration-300 border border-slate-100 hover:border-blue-100">
                    <div class="w-14 h-14 bg-white text-blue-600 rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Manajemen Stok</h3>
                    <p class="text-slate-500 leading-relaxed">Pantau jumlah produk, lokasi rak, dan nilai aset secara real-time dengan sistem peringatan stok menipis otomatis.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 rounded-2xl bg-slate-50 hover:bg-emerald-50 transition duration-300 border border-slate-100 hover:border-emerald-100">
                    <div class="w-14 h-14 bg-white text-emerald-600 rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Approval System</h3>
                    <p class="text-slate-500 leading-relaxed">Kontrol ketat arus barang keluar/masuk melalui verifikasi bertingkat oleh Warehouse Manager.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 rounded-2xl bg-slate-50 hover:bg-orange-50 transition duration-300 border border-slate-100 hover:border-orange-100">
                    <div class="w-14 h-14 bg-white text-orange-600 rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Restock Otomatis</h3>
                    <p class="text-slate-500 leading-relaxed">Siklus pembelian (PO) yang terintegrasi langsung dengan Supplier untuk mempercepat pengadaan barang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-slate-400 text-sm">
                &copy; {{ date('Y') }} <strong>WMSPRO</strong>. Sistem Manajemen Gudang Terpadu.
            </div>
            <div class="flex gap-6 text-sm font-medium text-slate-500">
                <a href="#" class="hover:text-blue-600">Privacy Policy</a>
                <a href="#" class="hover:text-blue-600">Terms of Service</a>
                <a href="#" class="hover:text-blue-600">Contact Support</a>
            </div>
        </div>
    </footer>

</body>
</html>