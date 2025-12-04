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
use Carbon\Carbon;

class RestockOrderController extends Controller
{
    // Konstruktor dimatikan karena isu middleware di Laravel 11
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin,manager,supplier');
    // }

    /**
     * Menampilkan daftar Restock Order.
     */
    public function index(Request $request)
    {
        $query = RestockOrder::with(['supplier', 'user'])
                             ->latest();
        
        // Filter berdasarkan peran:
        if (Auth::user()->isSupplier()) {
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
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Restock Order.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        
        // Ambil produk low stock atau habis
        $products = Product::whereColumn('current_stock', '<=', 'min_stock')
                           ->orWhere('current_stock', 0)
                           ->orderBy('name')
                           ->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        
        // Fallback: Jika tidak ada low stock, tampilkan semua
        if ($products->isEmpty()) {
             $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        }

        return view('restock_orders.create', compact('suppliers', 'products'));
    }

    /**
     * Menyimpan Restock Order baru.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();

        try {
            // Auto Generate PO Number
            $datePart = date('ym');
            $latestOrder = RestockOrder::where('po_number', 'like', "PO/{$datePart}/%")
                                       ->latest()
                                       ->first();
            
            $nextNumber = 1;
            if ($latestOrder) {
                $lastNumber = (int) substr($latestOrder->po_number, -3);
                $nextNumber = $lastNumber + 1;
            }
            $poNumber = "PO/{$datePart}/" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'status' => 'Pending',
            ]);

            $orderDetails = [];

            foreach ($request->products as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];
                
                if ($quantity <= 0) continue; 
                
                $orderDetails[] = new RestockOrderDetail([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
            
            $order->details()->saveMany($orderDetails);

            DB::commit();

            return redirect()->route('restock_orders.index')->with('success', "Restock Order #{$poNumber} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Order: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail.
     */
    public function show(RestockOrder $restockOrder)
    {
        $restockOrder->load('details.product', 'supplier');
        
        if (Auth::user()->isSupplier() && Auth::user()->supplier_id !== $restockOrder->supplier_id) {
             abort(403, 'Akses ditolak.');
        }

        return view('restock_orders.show', compact('restockOrder'));
    }

    /**
     * Konfirmasi Supplier.
     */
    public function confirmOrder(Request $request, RestockOrder $restockOrder)
    {
        if (!Auth::user()->isSupplier() || Auth::user()->supplier_id !== $restockOrder->supplier_id) {
            abort(403, 'Akses ditolak.');
        }
        if ($restockOrder->status !== 'Pending') {
            return redirect()->back()->with('error', 'Status pesanan tidak valid.');
        }
        
        $restockOrder->status = 'Confirmed by Supplier';
        $restockOrder->save();
        
        return redirect()->back()->with('success', "Pesanan #{$restockOrder->po_number} berhasil dikonfirmasi.");
    }
    
    /**
     * Update Status (Manager).
     */
    public function updateStatus(Request $request, RestockOrder $restockOrder)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses ditolak.');
        }
        
        $request->validate(['status' => ['required', Rule::in(['In Transit', 'Received'])]]);
        $newStatus = $request->status;

        if ($newStatus === 'In Transit' && $restockOrder->status !== 'Confirmed by Supplier') {
            return redirect()->back()->with('error', 'Pesanan harus dikonfirmasi supplier dulu.');
        }
        if ($newStatus === 'Received' && $restockOrder->status !== 'In Transit') {
            return redirect()->back()->with('error', 'Pesanan harus status In Transit dulu.');
        }

        $restockOrder->status = $newStatus;
        $restockOrder->save();
        
        if ($newStatus === 'Received') {
            return redirect()->back()->with('success', "Pesanan Diterima. Stok belum bertambah otomatis. Silakan buat Transaksi Barang Masuk.");
        }

        return redirect()->back()->with('success', "Status berhasil diperbarui menjadi {$newStatus}.");
    }
}