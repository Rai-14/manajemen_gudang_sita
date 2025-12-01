<?php

namespace App\Http\Controllers;

use App\Models\RestockOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\RestockOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RestockOrderController extends Controller
{
    /**
     * Konstruktor untuk membatasi akses berdasarkan peran.
     */
    public function __construct()
    {
        // Akses diizinkan untuk Admin, Manager, dan Supplier
        $this->middleware('auth');
        $this->middleware('role:admin,manager,supplier');
    }

    /**
     * Menampilkan daftar Restock Order.
     */
    public function index(Request $request)
    {
        $query = RestockOrder::with(['supplier', 'user'])
                             ->latest();
        
        // Filter berdasarkan peran:
        if (Auth::user()->isSupplier()) {
            // Supplier hanya melihat pesanan yang ditujukan kepada mereka
            $query->where('supplier_id', Auth::user()->supplier_id);
        }

        $restockOrders = $query->paginate(15);
        
        return view('restock_orders.index', compact('restockOrders'));
    }

    /**
     * Menampilkan form untuk membuat Restock Order baru (Hanya Manager/Admin).
     */
    public function create()
    {
        // Otorisasi internal tambahan (Karena middleware di atas mengizinkan Supplier)
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Restock Order.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        // Ambil data produk yang stoknya di bawah minimum (opsional, tapi disarankan)
        $products = Product::whereColumn('current_stock', '<=', 'min_stock')
                           ->orWhere('current_stock', 0)
                           ->orderBy('name')
                           ->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        
        // Jika tidak ada produk low stock, tampilkan semua produk
        if ($products->isEmpty()) {
             $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        }


        return view('restock_orders.create', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan Restock Order baru ke database (Hanya Manager/Admin).
     */
    public function store(Request $request)
    {
        // Logika Store 
        return redirect()->route('restock_orders.index')->with('success', 'Restock Order berhasil dibuat dan dikirim ke Supplier.');
    }
    
    
}