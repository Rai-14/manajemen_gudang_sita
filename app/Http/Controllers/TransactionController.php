<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'supplier'])->latest();

        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);

        $transactions = $query->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('transactions.create_type'); 
    }

    public function createIncoming()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']); 
        return view('transactions.incoming', compact('suppliers', 'products'));
    }

    public function storeIncoming(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        try {
            $prefix = 'IN/' . date('ym') . '/';
            $lastTrx = Transaction::where('transaction_number', 'like', $prefix . '%')->latest()->first();
            $nextNo = $lastTrx ? ((int)substr($lastTrx->transaction_number, -3) + 1) : 1;
            $trxNo = $prefix . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $trxNo,
                'type' => 'incoming',
                'user_id' => Auth::id(),
                'transaction_date' => $request->transaction_date,
                'supplier_id' => $request->supplier_id,
                'notes' => $request->notes,
                'status' => 'Pending',
            ]);

            foreach ($request->products as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', "Transaksi Masuk #$trxNo berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function createOutgoing()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
        return view('transactions.outgoing', compact('products'));
    }

    public function storeOutgoing(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'customer_name' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            if ($product->current_stock < $item['quantity']) {
                return back()->with('error', "Stok tidak cukup untuk produk {$product->name}. Sisa: {$product->current_stock}");
            }
        }

        DB::beginTransaction();
        try {
            $prefix = 'OUT/' . date('ym') . '/';
            $lastTrx = Transaction::where('transaction_number', 'like', $prefix . '%')->latest()->first();
            $nextNo = $lastTrx ? ((int)substr($lastTrx->transaction_number, -3) + 1) : 1;
            $trxNo = $prefix . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $trxNo,
                'type' => 'outgoing',
                'user_id' => Auth::id(),
                'transaction_date' => $request->transaction_date,
                'customer_name' => $request->customer_name,
                'status' => 'Pending',
            ]);

            foreach ($request->products as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', "Transaksi Keluar #$trxNo berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['details.product', 'user', 'supplier']);
        return view('transactions.show', compact('transaction'));
    }

    public function approve(Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Transaksi sudah selesai atau ditolak.');
        }

        DB::beginTransaction();
        try {
            foreach ($transaction->details as $detail) {
                $product = $detail->product;
                
                if ($transaction->type === 'incoming') {
                    $product->increment('current_stock', $detail->quantity);
                } else {
                    if ($product->current_stock < $detail->quantity) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi saat approval.");
                    }
                    $product->decrement('current_stock', $detail->quantity);
                }
            }

            $transaction->update(['status' => 'Approved']);
            DB::commit();

            return back()->with('success', 'Transaksi Disetujui! Stok telah diperbarui secara otomatis.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Tidak bisa menolak transaksi yang sudah diproses.');
        }

        $transaction->update(['status' => 'Rejected']);
        return back()->with('success', 'Transaksi Ditolak.');
    }

    // ====================================================
    // FITUR EDIT & HAPUS
    // ====================================================

    public function edit(Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return redirect()->route('transactions.index')->with('error', 'Transaksi yang sudah diproses tidak dapat diedit.');
        }

        if (Auth::user()->role === 'staff' && $transaction->user_id !== Auth::id()) {
            abort(403, 'Anda hanya dapat mengedit transaksi buatan sendiri.');
        }

        // PERBAIKAN: Load 'details.product' agar nama produk muncul di View Edit
        $transaction->load('details.product');
        
        if ($transaction->type === 'incoming') {
            $suppliers = Supplier::orderBy('name')->get();
            $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
            return view('transactions.edit_incoming', compact('transaction', 'suppliers', 'products'));
        } else {
            $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
            return view('transactions.edit_outgoing', compact('transaction', 'products'));
        }
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Tidak bisa mengedit transaksi yang bukan Pending.');
        }

        $request->validate([
            'transaction_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        if ($transaction->type === 'incoming') {
            $request->validate(['supplier_id' => 'required|exists:suppliers,id']);
        } else {
            $request->validate(['customer_name' => 'required|string']);
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                if ($product->current_stock < $item['quantity']) {
                    return back()->with('error', "Stok tidak cukup untuk {$product->name}. Sisa: {$product->current_stock}");
                }
            }
        }

        DB::beginTransaction();
        try {
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'supplier_id' => $transaction->type === 'incoming' ? $request->supplier_id : null,
                'customer_name' => $transaction->type === 'outgoing' ? $request->customer_name : null,
                'notes' => $request->notes,
            ]);

            // Hapus detail lama, ganti dengan yang baru dari form edit
            $transaction->details()->delete();

            foreach ($request->products as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Hanya transaksi dengan status Pending yang dapat dihapus.');
        }

        if (Auth::user()->role === 'staff' && $transaction->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menghapus transaksi ini.');
        }

        DB::beginTransaction();
        try {
            $transaction->details()->delete();
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}