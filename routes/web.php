<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController; // Sudah ada

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource Route untuk Produk
    Route::resource('products', ProductController::class);
    
    // Resource Route untuk Kategori
    Route::resource('categories', CategoryController::class);

    // Resource Route dan Custom Route untuk Transaksi
    Route::resource('transactions', TransactionController::class)->except(['create', 'store', 'edit', 'update', 'destroy']); // Transaksi punya custom edit/update/delete/show
    
    // Custom routes untuk Create, Store, Show, dan Approval
    Route::controller(TransactionController::class)->group(function () {
        Route::get('transactions/incoming/create', 'createIncoming')->name('transactions.create_incoming');
        Route::post('transactions/incoming', 'storeIncoming')->name('transactions.store_incoming');

        Route::get('transactions/outgoing/create', 'createOutgoing')->name('transactions.create_outgoing');
        Route::post('transactions/outgoing', 'storeOutgoing')->name('transactions.store_outgoing');
        
        // Route untuk Approval/Rejection
        Route::patch('transactions/{transaction}/approve', 'approve')->name('transactions.approve');
        Route::patch('transactions/{transaction}/reject', 'reject')->name('transactions.reject');
    });
});

require __DIR__.'/auth.php';