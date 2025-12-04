<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RestockOrderController;
use App\Http\Controllers\DashboardController; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index']) 
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource Route untuk Produk
    Route::resource('products', ProductController::class);
    
    // Resource Route untuk Kategori
    Route::resource('categories', CategoryController::class);
    
    // Custom routes untuk Transaksi
    Route::controller(TransactionController::class)->group(function () {
        // Halaman awal pilih tipe transaksi (Masuk/Keluar)
        Route::get('transactions/create', 'create')->name('transactions.create');

        // Route Barang Masuk
        Route::get('transactions/incoming/create', 'createIncoming')->name('transactions.create_incoming');
        Route::post('transactions/incoming', 'storeIncoming')->name('transactions.store_incoming');

        // Route Barang Keluar
        Route::get('transactions/outgoing/create', 'createOutgoing')->name('transactions.create_outgoing');
        Route::post('transactions/outgoing', 'storeOutgoing')->name('transactions.store_outgoing');
        
        // Approval
        Route::patch('transactions/{transaction}/approve', 'approve')->name('transactions.approve');
        Route::patch('transactions/{transaction}/reject', 'reject')->name('transactions.reject');
    });

    // Resource Route diletakkan DI BAWAH Custom Route
    Route::resource('transactions', TransactionController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // Resource Route untuk Restock Orders
    Route::resource('restock_orders', RestockOrderController::class)->except(['edit', 'update', 'destroy']);
    
    // Custom routes untuk Restock Aksi
    Route::controller(RestockOrderController::class)->group(function () {
        Route::patch('restock_orders/{restock_order}/confirm', 'confirmOrder')->name('restock_orders.confirm');
        Route::patch('restock_orders/{restock_order}/update-status', 'updateStatus')->name('restock_orders.update_status');
    });
});

require __DIR__.'/auth.php';