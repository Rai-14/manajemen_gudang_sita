<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\RestockOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon; // Untuk perhitungan tanggal

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard sesuai peran pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        $data = [];
        
        // --- LOGIKA DASHBOARD ADMIN/MANAGER ---
        if ($user->isAdmin() || $user->isManager()) {
            $currentMonth = Carbon::now()->startOfMonth();
            
            $data['total_products'] = Product::count();
            // Produk yang stoknya <= min_stock
            $data['total_low_stock'] = Product::whereColumn('current_stock', '<=', 'min_stock')->count();
            
            $data['pending_transactions'] = Transaction::where('status', 'Pending')->count();
            $data['pending_restock_orders'] = RestockOrder::where('status', 'Pending')->count();

            // Total transaksi bulan ini
            $data['transactions_month'] = Transaction::whereMonth('created_at', $currentMonth->month)
                                                    ->whereYear('created_at', $currentMonth->year)
                                                    ->count();

            // 5 Produk dengan stok terendah
            $data['low_stock_products'] = Product::with('category')
                                                 ->whereColumn('current_stock', '<=', 'min_stock')
                                                 ->orderBy('current_stock', 'asc')
                                                 ->limit(5)
                                                 ->get();
            
            // 5 Transaksi pending terbaru
            $data['latest_pending_transactions'] = Transaction::with('user', 'supplier')
                                                              ->where('status', 'Pending')
                                                              ->latest()
                                                              ->limit(5)
                                                              ->get();
        } 
        
        // --- LOGIKA DASHBOARD STAFF GUDANG ---
        elseif ($user->isStaff()) {
            $data['my_recent_transactions'] = Transaction::where('user_id', $user->id)
                                                          ->latest()
                                                          ->limit(5)
                                                          ->get();
            $data['total_pending_by_me'] = Transaction::where('user_id', $user->id)
                                                      ->where('status', 'Pending')
                                                      ->count();
        }
        
        // --- LOGIKA DASHBOARD SUPPLIER ---
        elseif ($user->isSupplier()) {
            $data['orders_to_confirm'] = RestockOrder::where('supplier_id', $user->supplier_id)
                                                      ->where('status', 'Pending')
                                                      ->count();
            $data['latest_orders'] = RestockOrder::with('user')
                                                  ->where('supplier_id', $user->supplier_id)
                                                  ->latest()
                                                  ->limit(5)
                                                  ->get();
        }
        
        return view('dashboard', $data);
    }
}