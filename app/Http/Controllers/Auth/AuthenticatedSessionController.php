<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // <--- PENTING: Untuk menampilkan error message

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Cek Email & Password (Login Bawaan Laravel)
        $request->authenticate();

        $request->session()->regenerate();

        // ==========================================================
        // 2. LOGIKA SATPAM: CEK KECOCOKAN PINTU vs ROLE
        // ==========================================================
        
        $user = $request->user();
        $portal = $request->input('login_portal'); // Mengambil 'staff' atau 'supplier' dari form

        // SKENARIO A: Login lewat Halaman SUPPLIER
        if ($portal === 'supplier') {
            // Jika role user BUKAN supplier (berarti dia admin/manager/staff)
            if ($user->role !== 'supplier') {
                Auth::guard('web')->logout(); // Tendang keluar
                
                throw ValidationException::withMessages([
                    'email' => 'Akses Ditolak: Akun Staff/Manager tidak boleh login di sini.',
                ]);
            }
        }

        // SKENARIO B: Login lewat Halaman STAFF
        if ($portal === 'staff') {
            // Jika role user ADALAH supplier
            if ($user->role === 'supplier') {
                Auth::guard('web')->logout(); // Tendang keluar
                
                throw ValidationException::withMessages([
                    'email' => 'Akses Ditolak: Akun Supplier silakan login di Portal Mitra.',
                ]);
            }
        }
        
        // Catatan: Admin, Manager, dan Staff diperbolehkan masuk di portal 'staff'
        // karena logika di atas hanya menolak jika role === 'supplier'.

        // 3. Jika Lolos, Arahkan ke Dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}