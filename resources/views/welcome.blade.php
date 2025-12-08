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

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { slate: { 850: '#1e293b' } },
                    animation: { blob: "blob 7s infinite" },
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
                            <a href="{{ route('portal.akses') }}" class="text-sm font-bold text-slate-700 hover:text-blue-600 px-4 py-2 transition">Log in</a>

                            {{-- PERBAIKAN: Mengarah ke 'supplier.register' --}}
                            @if (Route::has('supplier.register'))
                                <a href="{{ route('supplier.register') }}" class="text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5">Register Supplier</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-slate-900 overflow-hidden min-h-screen flex items-center pt-16">
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
                        Platform manajemen inventori modern untuk mencatat barang masuk, keluar, dan restock order secara real-time.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition transform hover:-translate-y-1 text-center flex items-center justify-center gap-2">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('portal.akses') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition transform hover:-translate-y-1 text-center">
                                Login Staff / Manager
                            </a>
                            
                            {{-- PERBAIKAN: Tombol Register di tengah --}}
                            @if (Route::has('supplier.register'))
                                <a href="{{ route('supplier.register') }}" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl border border-slate-700 transition text-center hover:border-slate-500">
                                    Daftar Mitra Supplier
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Visual Content (Hiasan) -->
                <div class="lg:w-1/2 relative w-full max-w-md lg:max-w-full hidden lg:block">
                    <!-- Blob Animation -->
                    <div class="absolute -top-20 -right-20 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[100px] opacity-30 animate-blob"></div>
                    <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-cyan-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-30 animate-blob animation-delay-2000"></div>

                    <!-- Card Mockup -->
                    <div class="relative rounded-2xl bg-slate-800/80 border border-slate-700 p-6 shadow-2xl backdrop-blur-xl rotate-2 hover:rotate-0 transition duration-500">
                        <div class="flex justify-between items-center mb-6 border-b border-slate-700 pb-4">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <div class="text-xs font-mono text-slate-500">WMS Dashboard</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-700/50 p-4 rounded-xl border border-slate-600">
                                <div class="text-xs text-slate-400 mb-1">Total Stok</div>
                                <div class="text-2xl font-bold text-white">1,240</div>
                            </div>
                            <div class="bg-slate-700/50 p-4 rounded-xl border border-slate-600">
                                <div class="text-xs text-slate-400 mb-1">Pesanan</div>
                                <div class="text-2xl font-bold text-white">25</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>