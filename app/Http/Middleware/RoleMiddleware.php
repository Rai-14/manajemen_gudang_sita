<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Import Auth

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string[]  ...$roles Daftar peran yang diizinkan (misal: 'admin', 'manager')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah pengguna terautentikasi
        if (!Auth::check()) {
            // Jika tidak terautentikasi, redirect ke halaman login
            return redirect('login');
        }

        // Ambil objek pengguna
        $user = Auth::user();

        // 2. Cek apakah peran pengguna ada dalam daftar peran yang diizinkan
        // in_array(nilai_yang_dicari, array_tempat_mencari)
        if (in_array($user->role, $roles)) {
            // Jika peran diizinkan, lanjutkan permintaan
            return $next($request);
        }

        // 3. Jika pengguna terautentikasi tetapi peran tidak diizinkan
        // Redirect ke dashboard dengan pesan error.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin akses ke halaman ini.');
    }
}