<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Staff - WMS Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* --- Background Premium --- */
        .bg-landing {
            background: radial-gradient(circle at 10% 20%, rgb(17, 24, 39) 0%, rgb(15, 60, 133) 90%);
            min-height: 100vh;
        }

        /* --- Input Field Premium --- */
        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        .input-premium {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 14px 15px 14px 50px; /* Space for icon */
            width: 100%;
            border-radius: 12px;
            outline: none;
            font-size: 0.95rem;
            color: #334155;
            transition: 0.3s;
        }
        .input-premium:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .input-premium:focus + i {
            color: #3b82f6;
        }

        /* --- Button Gradient --- */
        .btn-gradient {
            background-size: 200% auto;
            background-image: linear-gradient(to right, #2563eb 0%, #06b6d4 51%, #2563eb 100%);
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: 0.5s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }
        .btn-gradient:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -5px rgba(37, 99, 235, 0.5);
        }
    </style>
</head>
<body class="bg-landing flex items-center justify-center p-6">

    <!-- Card Container -->
    <div class="bg-white w-full max-w-[450px] rounded-3xl shadow-2xl p-8 md:p-10 relative overflow-hidden animate-fade-in-up">
        
        <!-- Decoration Top -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600"></div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-blue-600 text-2xl shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Staff Portal</h1>
            <p class="text-slate-400 text-sm mt-2">Masuk untuk mengakses Dashboard Manager</p>
        </div>

        <!-- Form Login Laravel -->
        <!-- Pastikan route 'login' ada di web.php Anda (bawaan Breeze/Auth) -->
        <form method="POST" action="{{ route('login') }}">
            <!-- Token CSRF Wajib untuk Laravel -->
            @csrf 

            <!-- Input Email -->
            <div class="input-group">
                <input type="email" name="email" class="input-premium" placeholder="Email Perusahaan" required autofocus>
                <i class="fas fa-envelope"></i>
            </div>

            <!-- Input Password -->
            <div class="input-group">
                <input type="password" name="password" class="input-premium" placeholder="Kata Sandi" required>
                <i class="fas fa-lock"></i>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mb-6 text-sm">
                <label class="flex items-center text-slate-500 cursor-pointer hover:text-slate-700">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>Ingat Saya</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-600 font-medium hover:text-blue-700 hover:underline">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="btn-gradient">
                Masuk Sekarang
            </button>

            <!-- Link Kembali -->
            <div class="text-center mt-6">
                <a href="{{ url('/') }}" class="text-slate-400 text-sm hover:text-slate-600 flex items-center justify-center gap-2 transition">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

</body>
</html>