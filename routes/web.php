<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\ProductController;

use App\Http\Controllers\CategoryController;

use App\Http\Controllers\TransactionController;

use App\Http\Controllers\RestockOrderController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\ReportController; // <--- TAMBAHAN: Import Controller Laporan



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

*/



Route::get('/', function () { return view('welcome'); });

Route::get('/portal-akses', function () { return view('staff_manager_login'); })->name('portal.akses');

Route::get('/portal-supplier', function () { return view('supplier_login'); })->name('portal.supplier');



Route::middleware(['auth', 'verified'])->group(function () {

   

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    Route::get('/search', function () {

        return view('search.results');

    })->name('search.global');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // ====================================================

    // ZONA KHUSUS ADMIN

    // ====================================================

    Route::middleware(['role:admin'])->group(function () {

        Route::resource('users', UserController::class);

    });



    // ====================================================

    // ZONA ADMIN & MANAGER (Master Data, Approval & LAPORAN)

    // ====================================================

    Route::middleware(['role:admin,manager'])->group(function () {

        Route::resource('categories', CategoryController::class);

        Route::resource('products', ProductController::class);



        Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');

        Route::patch('transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');



        // --- TAMBAHAN BARU: REPORTING ---

        Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');

        Route::get('reports/print', [ReportController::class, 'printInventory'])->name('reports.print');

    });



    // ====================================================

    // ZONA KHUSUS MANAGER (Tanpa Admin)

    // ====================================================

    Route::middleware(['role:manager'])->group(function () {

        Route::get('restock_orders/create', [RestockOrderController::class, 'create'])->name('restock_orders.create');

        Route::post('restock_orders', [RestockOrderController::class, 'store'])->name('restock_orders.store');

        Route::patch('restock_orders/{restock_order}/update-status', [RestockOrderController::class, 'updateStatus'])->name('restock_orders.update_status');

    });



    // ====================================================

    // ZONA STAFF GUDANG

    // ====================================================

    Route::middleware(['role:staff,manager,admin'])->group(function () {

        Route::controller(TransactionController::class)->group(function () {

            Route::get('transactions/create', 'create')->name('transactions.create');

            Route::get('transactions/incoming/create', 'createIncoming')->name('transactions.create_incoming');

            Route::post('transactions/incoming', 'storeIncoming')->name('transactions.store_incoming');

            Route::get('transactions/outgoing/create', 'createOutgoing')->name('transactions.create_outgoing');

            Route::post('transactions/outgoing', 'storeOutgoing')->name('transactions.store_outgoing');

        });

        Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

    });

   

    // ====================================================

    // ZONA SUPPLIER

    // ====================================================

    Route::middleware(['role:supplier'])->group(function () {

        Route::patch('restock_orders/{restock_order}/confirm', [RestockOrderController::class, 'confirmOrder'])->name('restock_orders.confirm');

    });



    // ====================================================

    // ZONA LIST RESTOCK (Manager & Supplier Saja)

    // ====================================================

    Route::middleware(['role:manager,supplier'])->group(function () {

        Route::get('restock_orders', [RestockOrderController::class, 'index'])->name('restock_orders.index');

        Route::get('restock_orders/{restock_order}', [RestockOrderController::class, 'show'])->name('restock_orders.show');

    });



});



require __DIR__.'/auth.php';