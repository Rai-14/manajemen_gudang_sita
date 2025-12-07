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

        $restockOrders = $query->paginate(15);
        return view('restock_orders.index', compact('restockOrders'));
    }

    public function create()
    {
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

    public function updateStatus(Request $request, RestockOrder $restockOrder)
    {
        $user = Auth::user();

        // 1. CEK PERMISSION
        if ($user->isSupplier()) {
            if ($restockOrder->supplier_id !== $user->supplier_id) {
                abort(403, 'Akses ditolak. Ini bukan pesanan untuk Anda.');
            }
        } elseif (!$user->isManager()) {
            abort(403, 'Akses ditolak.');
        }
        
        // 2. VALIDASI STATUS (Gunakan 'In Transit' sebagai standar)
        $request->validate([
            'status' => ['required', Rule::in(['In Transit', 'Received'])]
        ]);
        
        $newStatus = $request->status;

        // 3. LOGIKA PERUBAHAN STATUS
        
        // Kasus A: Supplier mengirim barang (Pending -> In Transit)
        if ($newStatus === 'In Transit') {
             if (!in_array($restockOrder->status, ['Pending', 'Confirmed by Supplier'])) {
                 return redirect()->back()->with('error', 'Pesanan tidak bisa dikirim karena status saat ini tidak valid.');
             }
             
             // PERBAIKAN: Gunakan 'In Transit' agar sesuai Database ENUM
             $restockOrder->status = 'In Transit';
             $restockOrder->save();
             
             return redirect()->back()->with('success', "Barang berhasil dikirim (Status: In Transit).");
        }

        // Kasus B: Manager menerima barang (In Transit -> Received)
        if ($newStatus === 'Received') {
            if (!Auth::user()->isManager()) {
                abort(403, 'Hanya Manager yang bisa menerima barang.');
            }
            
            if ($restockOrder->status !== 'In Transit') {
                return redirect()->back()->with('error', 'Pesanan belum dikirim oleh supplier (harus In Transit).');
            }
            
            $restockOrder->status = 'Received';
            $restockOrder->save();
            
            return redirect()->back()->with('success', "Pesanan Diterima. Silakan buat Transaksi Barang Masuk.");
        }

        return redirect()->back()->with('error', "Status tidak dikenali.");
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
}