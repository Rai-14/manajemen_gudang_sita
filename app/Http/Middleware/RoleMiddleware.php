<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles (Daftar peran yang diizinkan)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Cek apakah peran pengguna ada di dalam daftar yang diizinkan
        // Logika: Jika role user ada di dalam array $roles, silakan lewat.
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 3. JIKA DITOLAK:
        
        // Jika request berupa AJAX/API (menghindari error redirect di background)
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Tampilkan halaman Error 403 (Forbidden) bawaan Laravel
        // Ini lebih aman daripada redirect, agar user tahu mereka salah kamar.
        abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk halaman ini.');
    }
}