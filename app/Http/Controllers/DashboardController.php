<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\RestockOrder;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            $data['latest_pending_transactions'] = Transaction::with('user')
                                                             ->where('status', 'Pending')
                                                             ->latest()
                                                             ->limit(5)
                                                             ->get();

            // GRAFIK ADMIN
            $months = collect([]);
            $masukData = collect([]);
            $keluarData = collect([]);

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months->push($date->isoFormat('MMM')); 

                $totalIncoming = Transaction::where('type', 'incoming')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where('status', 'Verified')
                    ->count();

                $totalOutgoing = Transaction::where('type', 'outgoing')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where('status', 'Shipped')
                    ->count();

                $masukData->push($totalIncoming);
                $keluarData->push($totalOutgoing);
            }
            
            $data['labels'] = $months->toArray();
            $data['masuk'] = $masukData->toArray();
            $data['keluar'] = $keluarData->toArray();
            
            // PIE CHART KATEGORI
            $stockDistribution = Product::select('category_id', DB::raw('SUM(current_stock) as total_stock'))
                ->groupBy('category_id')
                ->where('current_stock', '>', 0)
                ->get();
            
            $categoryLabels = [];
            $categoryData = [];
            $categoryIds = $stockDistribution->pluck('category_id')->unique()->toArray();
            $categories = Category::whereIn('id', $categoryIds)->pluck('name', 'id');

            foreach ($stockDistribution as $stock) {
                $categoryLabels[] = $categories[$stock->category_id] ?? 'Tanpa Kategori';
                $categoryData[] = $stock->total_stock;
            }

            $data['kategori_labels'] = $categoryLabels;
            $data['kategori_data'] = $categoryData;
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
        
        // --- LOGIKA DASHBOARD SUPPLIER (UPDATED) ---
        elseif ($user->isSupplier()) {
            $supplierId = $user->supplier_id ?? null;

            // Menambahkan ->with('details.product')
            // Agar nama barang bisa diambil di View
            
            // 1. Pesanan Baru
            $data['active_orders'] = RestockOrder::with(['user', 'details.product']) 
                                                ->where('supplier_id', $supplierId)
                                                ->whereIn('status', ['Pending', 'Confirmed by Supplier']) 
                                                ->orderBy('created_at', 'asc')
                                                ->get();

            // 2. Sedang Dikirim
            $data['shipped_orders'] = RestockOrder::with(['user', 'details.product'])
                                                ->where('supplier_id', $supplierId)
                                                ->where('status', 'In Transit') 
                                                ->orderBy('updated_at', 'desc')
                                                ->get();

            // 3. Riwayat Selesai
            $data['completed_orders'] = RestockOrder::with(['user', 'details.product'])
                                                ->where('supplier_id', $supplierId)
                                                ->where('status', 'Received')
                                                ->orderBy('updated_at', 'desc')
                                                ->limit(10)
                                                ->get();
            
            $data['orders_to_confirm'] = $data['active_orders']->count();
            
            return view('dashboard.supplier', $data);
        }
        
        return view('dashboard', $data);
    }
}