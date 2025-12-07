<?php



namespace App\Http\Controllers;



use App\Models\Product;

use App\Models\Transaction;

use App\Models\RestockOrder;

use App\Models\Category; // Import Model Category

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB; // Untuk agregasi data

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

            $data['latest_pending_transactions'] = Transaction::with('user') // Mengasumsikan relasi user ada

                                                             ->where('status', 'Pending')

                                                             ->latest()

                                                             ->limit(5)

                                                             ->get();



            // ======================================================

            // LOGIKA DATA GRAFIK (6 BULAN TERAKHIR - FIX DATA REAL)

            // ======================================================

           

            $months = collect([]);

            $masukData = collect([]);

            $keluarData = collect([]);



            for ($i = 5; $i >= 0; $i--) {

                $date = Carbon::now()->subMonths($i);

                $months->push($date->isoFormat('MMM')); // Format: Jun, Jul, Ags...



                // Ambil total transaksi masuk (verified)

                $totalIncoming = Transaction::where('type', 'incoming')

                    ->whereMonth('created_at', $date->month)

                    ->whereYear('created_at', $date->year)

                    ->where('status', 'Verified')

                    ->count();



                // Ambil total transaksi keluar (shipped/approved)

                $totalOutgoing = Transaction::where('type', 'outgoing')

                    ->whereMonth('created_at', $date->month)

                    ->whereYear('created_at', $date->year)

                    ->where('status', 'Shipped') // Sesuaikan status akhir transaksi keluar Anda

                    ->count();



                $masukData->push($totalIncoming);

                $keluarData->push($totalOutgoing);

            }

           

            // Persiapkan data untuk JavaScript

            $data['labels'] = $months->toArray();

            $data['masuk'] = $masukData->toArray();

            $data['keluar'] = $keluarData->toArray();

           

            // ======================================================

            // LOGIKA DISTRIBUSI STOK PER KATEGORI (FIX DATA REAL)

            // ======================================================



            // Menghitung total stok per kategori

            $stockDistribution = Product::select('category_id', DB::raw('SUM(current_stock) as total_stock'))

                ->groupBy('category_id')

                ->where('current_stock', '>', 0)

                ->get();

           

            $categoryLabels = [];

            $categoryData = [];



            // Fetch semua nama kategori yang terlibat

            $categoryIds = $stockDistribution->pluck('category_id')->unique()->toArray();

            $categories = Category::whereIn('id', $categoryIds)->pluck('name', 'id');



            foreach ($stockDistribution as $stock) {

                // Pastikan category_id tidak null dan ada di tabel categories

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

       

        // --- LOGIKA DASHBOARD SUPPLIER ---

        elseif ($user->isSupplier()) {

            // Asumsikan relasi supplier_id ada di model User atau sudah disiapkan

            $supplierId = $user->supplier_id ?? null;



            $data['orders_to_confirm'] = RestockOrder::where('supplier_id', $supplierId)

                                                     ->where('status', 'Pending')

                                                     ->count();

            $data['latest_orders'] = RestockOrder::with('user')

                                                 ->where('supplier_id', $supplierId)

                                                 ->latest()

                                                 ->limit(5)

                                                 ->get();

        }

       

        return view('dashboard', $data);

    }

}