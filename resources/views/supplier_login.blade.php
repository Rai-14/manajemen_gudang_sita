<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Supplier - Mitra WMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* --- Background Supplier (Nuansa Hijau/Teal Gelap) --- */
        .bg-supplier {
            background: radial-gradient(circle at 80% 20%, rgb(6, 78, 59) 0%, rgb(15, 23, 42) 90%);
            min-height: 100vh;
        }

        /* --- Input Field --- */
        .input-group { position: relative; width: 100%; margin-bottom: 1.5rem; }
        .input-group i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 1.1rem; transition: 0.3s;
        }
        .input-premium {
            background-color: #f8fafc; border: 2px solid #e2e8f0;
            padding: 14px 15px 14px 50px; width: 100%; border-radius: 12px;
            outline: none; font-size: 0.95rem; color: #334155; transition: 0.3s;
        }
        .input-premium:focus {
            background-color: #fff; border-color: #10b981; /* Hijau saat fokus */
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }
        .input-premium:focus + i { color: #10b981; }

        /* --- Button Gradient (Hijau) --- */
        .btn-green {
            background-size: 200% auto;
            background-image: linear-gradient(to right, #059669 0%, #34d399 51%, #059669 100%);
            color: white; font-weight: 600; padding: 14px; border-radius: 12px;
            border: none; width: 100%; cursor: pointer; transition: 0.5s;
            text-transform: uppercase; letter-spacing: 0.5px;
            box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.4);
        }
        .btn-green:hover {
            background-position: right center; transform: translateY(-2px);
            box-shadow: 0 15px 20px -5px rgba(5, 150, 105, 0.5);
        }
    </style>
</head>
<body class="bg-supplier flex items-center justify-center p-6">

    <div class="bg-white w-full max-w-[450px] rounded-3xl shadow-2xl p-8 md:p-10 relative overflow-hidden">
        <!-- Decoration Top (Hijau) -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-emerald-500 via-teal-400 to-green-600"></div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-emerald-600 text-2xl shadow-sm">
                <i class="fas fa-handshake"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Supplier Area</h1>
            <p class="text-slate-400 text-sm mt-2">Masuk untuk kelola stok dan pengiriman</p>
        </div>

        <!-- Form Login Supplier -->
        <!-- Perhatikan route-nya beda (nanti kita buat route custom) -->
        <form method="POST" action="{{ route('login') }}"> <!-- Ubah action jika pakai guard khusus -->
            @csrf 

            <div class="input-group">
                <input type="email" name="email" class="input-premium" placeholder="Email Perusahaan" required autofocus>
                <i class="fas fa-envelope-open-text"></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" class="input-premium" placeholder="Kata Sandi Mitra" required>
                <i class="fas fa-lock"></i>
            </div>

            <div class="flex items-center justify-between mb-6 text-sm">
                <label class="flex items-center text-slate-500 cursor-pointer hover:text-slate-700">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Ingat Saya</span>
                </label>
                <a href="#" class="text-emerald-600 font-medium hover:text-emerald-700 hover:underline">Lupa Password?</a>
            </div>

            <button type="submit" class="btn-green">
                Login Mitra
            </button>

            <!-- Link Register -->
            <div class="text-center mt-6 pt-4 border-t border-slate-100">
                <p class="text-slate-500 text-sm">Belum jadi mitra kami?</p>
                <a href="#" class="text-emerald-600 font-bold hover:underline mt-1 inline-block">Daftar Supplier Baru</a>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="text-slate-400 text-xs hover:text-slate-600 transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

</body>
</html>