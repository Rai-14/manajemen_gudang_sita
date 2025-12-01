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
use Carbon\Carbon; // Import Carbon untuk tanggal

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
        // Otorisasi internal tambahan
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Restock Order.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        // Ambil data produk yang stoknya di bawah minimum atau 0
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
        // Otorisasi internal tambahan
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk membuat Restock Order.');
        }

        // 1. Validasi Data
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
            // 2. Buat Nomor PO (Auto-generated: PO/YYMM/XXX)
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

            // 3. Simpan Restock Order Utama
            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(), // Manager yang membuat
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'status' => 'Pending', // Status awal: Menunggu Konfirmasi Supplier
            ]);

            $orderDetails = [];

            // 4. Simpan Detail Restock
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

            // 5. Commit Transaksi Database
            DB::commit();

            return redirect()->route('restock_orders.index')->with('success', "Restock Order #{$poNumber} berhasil dibuat dan dikirim ke Supplier.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Restock Order. Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail Restock Order.
     */
    public function show(RestockOrder $restockOrder)
    {
        // Pastikan relasi details dimuat bersama produk
        $restockOrder->load('details.product', 'supplier');
        
        // Otorisasi: Manager/Admin harus bisa melihat. Supplier hanya boleh melihat PO mereka.
        if (Auth::user()->isSupplier() && Auth::user()->supplier_id !== $restockOrder->supplier_id) {
             abort(403, 'Anda tidak memiliki izin untuk melihat pesanan ini.');
        }

        return view('restock_orders.show', compact('restockOrder'));
    }

    /**
     * Logika untuk Supplier mengkonfirmasi atau menolak order.
     */
    public function confirmOrder(Request $request, RestockOrder $restockOrder)
    {
        // 1. Otorisasi dan Status Check
        if (!Auth::user()->isSupplier() || Auth::user()->supplier_id !== $restockOrder->supplier_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengkonfirmasi pesanan ini.');
        }
        if ($restockOrder->status !== 'Pending') {
            return redirect()->back()->with('error', 'Pesanan sudah dikonfirmasi atau dibatalkan.');
        }
        
        // 2. Update Status menjadi Confirmed by Supplier
        $restockOrder->status = 'Confirmed by Supplier';
        $restockOrder->save();
        
        return redirect()->back()->with('success', "Pesanan #{$restockOrder->po_number} berhasil dikonfirmasi dan sedang diproses.");
    }
    
    /**
     * Logika untuk Manager/Admin update status pengiriman (In Transit / Received).
     */
    public function updateStatus(Request $request, RestockOrder $restockOrder)
    {
        // 1. Otorisasi dan Validasi
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status pengiriman.');
        }
        
        $request->validate(['status' => ['required', Rule::in(['In Transit', 'Received'])]]);
        $newStatus = $request->status;

        // Cek Status Transisi
        if ($newStatus === 'In Transit' && $restockOrder->status !== 'Confirmed by Supplier') {
            return redirect()->back()->with('error', 'Status tidak valid. Pesanan harus dalam status "Confirmed by Supplier" sebelum menjadi "In Transit".');
        }
        if ($newStatus === 'Received' && $restockOrder->status !== 'In Transit') {
            return redirect()->back()->with('error', 'Status tidak valid. Pesanan harus dalam status "In Transit" sebelum menjadi "Received".');
        }

        // 2. Update Status
        $restockOrder->status = $newStatus;
        $restockOrder->save();
        
        // 3. Tambahkan Notifikasi Khusus saat Received
        if ($newStatus === 'Received') {
            $message = "Status pesanan #{$restockOrder->po_number} diubah menjadi Received. PENTING: Staff Gudang harus membuat Transaksi Barang Masuk secara manual.";
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('success', "Status pesanan #{$restockOrder->po_number} berhasil diubah menjadi {$newStatus}.");
    }
}