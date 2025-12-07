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
    public function index(Request $request)
    {
        $query = RestockOrder::with(['supplier', 'user'])->latest();
        
        if (Auth::user()->isSupplier()) {
            $query->where('supplier_id', Auth::user()->supplier_id);
        }

        // Opsional: Jika Admin tetap mencoba masuk (meski dihalangi middleware), kita bisa return kosong atau error
        // Tapi middleware di routes/web.php sudah menangani ini.

        $restockOrders = $query->paginate(15);
        return view('restock_orders.index', compact('restockOrders'));
    }

    public function create()
    {
        // REVISI: Hapus isAdmin()
        if (!Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak. Hanya Warehouse Manager.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::whereColumn('current_stock', '<=', 'min_stock')
                           ->orWhere('current_stock', 0)
                           ->orderBy('name')
                           ->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        
        if ($products->isEmpty()) {
             $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'min_stock', 'current_stock']);
        }

        return view('restock_orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // REVISI: Hapus isAdmin()
        if (!Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak.');
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
            $datePart = date('ym');
            $latestOrder = RestockOrder::where('po_number', 'like', "PO/{$datePart}/%")->latest()->first();
            $nextNumber = $latestOrder ? ((int) substr($latestOrder->po_number, -3) + 1) : 1;
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

            foreach ($request->products as $item) {
                if ((int)$item['quantity'] > 0) {
                    RestockOrderDetail::create([
                        'restock_order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                    ]);
                }
            }
            
            DB::commit();
            return redirect()->route('restock_orders.index')->with('success', "Restock Order #{$poNumber} berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Order: ' . $e->getMessage());
        }
    }
    
    public function show(RestockOrder $restockOrder)
    {
        $restockOrder->load('details.product', 'supplier');
        
        if (Auth::user()->isSupplier() && Auth::user()->supplier_id !== $restockOrder->supplier_id) {
             abort(403, 'Akses ditolak.');
        }

        return view('restock_orders.show', compact('restockOrder'));
    }

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
    
    public function updateStatus(Request $request, RestockOrder $restockOrder)
    {
        // REVISI: Hapus isAdmin()
        if (!Auth::user()->isManager()) {
            abort(403, 'Akses ditolak. Hanya Manager.');
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
            return redirect()->back()->with('success', "Pesanan Diterima. Silakan buat Transaksi Barang Masuk.");
        }

        return redirect()->back()->with('success', "Status berhasil diperbarui menjadi {$newStatus}.");
    }
}