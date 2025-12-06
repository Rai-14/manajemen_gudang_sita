<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RestockOrderController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HALAMAN PUBLIK (Tamu) ---
Route::get('/', function () { return view('welcome'); });

// Rute Halaman Login (Hanya Tampilan)
Route::get('/portal-akses', function () { return view('staff_manager_login'); })->name('portal.akses');
Route::get('/portal-supplier', function () { return view('supplier_login'); })->name('portal.supplier');

// --- RUTE SETELAH LOGIN (DILINDUNGI) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. DASHBOARD (Satu rute, nanti Controller yang membedakan tampilan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFILE (Semua user bisa edit profil sendiri)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ZONA KHUSUS ADMIN (Super User)
    // ====================================================
    Route::middleware(['role:admin'])->group(function () {
        // Kelola Pengguna (CRUD)
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });

    // ZONA 1: ADMIN & MANAGER (Pengambil Keputusan)
    // Hanya Admin dan Manager yang boleh masuk sini
    Route::middleware(['role:admin,manager'])->group(function () {
        // Kelola Master Data (Kategori & Produk)
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);

        // Approval Transaksi (Manager menyetujui transaksi besar)
        Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        Route::patch('transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');

        // Kelola Restock (Manager request barang ke supplier)
        Route::resource('restock_orders', RestockOrderController::class);
        Route::patch('restock_orders/{restock_order}/confirm', [RestockOrderController::class, 'confirmOrder'])->name('restock_orders.confirm');
    });

    // ZONA 2: STAFF GUDANG (Operasional Harian)
    // Staff boleh akses, Admin & Manager juga boleh (untuk monitoring/bantu)
    Route::middleware(['role:staff,manager,admin'])->group(function () {
        
        // Transaksi Barang Masuk & Keluar
        Route::controller(TransactionController::class)->group(function () {
            // Halaman Pilih Tipe
            Route::get('transactions/create', 'create')->name('transactions.create');
            
            // Barang Masuk
            Route::get('transactions/incoming/create', 'createIncoming')->name('transactions.create_incoming');
            Route::post('transactions/incoming', 'storeIncoming')->name('transactions.store_incoming');

            // Barang Keluar
            Route::get('transactions/outgoing/create', 'createOutgoing')->name('transactions.create_outgoing');
            Route::post('transactions/outgoing', 'storeOutgoing')->name('transactions.store_outgoing');
        });

        // Melihat Daftar Transaksi (Read Only untuk list)
        Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
    });

    // ZONA 3: SUPPLIER (Mitra Eksternal)
    // Hanya Supplier yang boleh masuk sini
    Route::middleware(['role:supplier'])->group(function () {
        // Update status pengiriman barang restock
        Route::patch('restock_orders/{restock_order}/update-status', [RestockOrderController::class, 'updateStatus'])->name('restock_orders.update_status');
        
        // (Nanti) Halaman lihat order khusus supplier
    });

});

require __DIR__.'/auth.php';