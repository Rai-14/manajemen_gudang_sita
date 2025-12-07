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
    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'supplier'])->latest();

        // Filter: Staff hanya lihat punya sendiri
        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        // Filter Tipe & Status
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);

        $transactions = $query->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Halaman Pilih Tipe Transaksi
     */
    public function create()
    {
        return view('transactions.create_type'); 
    }

    /**
     * Form Barang Masuk
     */
    public function createIncoming()
    {
        $suppliers = Supplier::orderBy('name')->get();
        // Menggunakan 'current_stock' sesuai database Anda
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']); 
        return view('transactions.incoming', compact('suppliers', 'products'));
    }

    /**
     * Simpan Barang Masuk
     */
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
            return redirect()->route('transactions.index')->with('success', "Transaksi Masuk #$trxNo berhasil dibuat. Menunggu persetujuan Manager.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Form Barang Keluar
     */
    public function createOutgoing()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
        return view('transactions.outgoing', compact('products'));
    }

    /**
     * Simpan Barang Keluar
     */
    public function storeOutgoing(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'customer_name' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Cek Stok Dulu
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
            return redirect()->route('transactions.index')->with('success', "Transaksi Keluar #$trxNo berhasil dibuat. Menunggu persetujuan Manager.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Detail Transaksi & Halaman Approval
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['details.product', 'user', 'supplier']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Manager Menyetujui Transaksi (UPDATE STOK REAL-TIME)
     */
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
                    // Masuk: Tambah current_stock
                    $product->increment('current_stock', $detail->quantity);
                } else {
                    // Keluar: Kurangi current_stock
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

    /**
     * Manager Menolak Transaksi
     */
    public function reject(Transaction $transaction)
    {
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Tidak bisa menolak transaksi yang sudah diproses.');
        }

        $transaction->update(['status' => 'Rejected']);
        return back()->with('success', 'Transaksi Ditolak.');
    }

    // ====================================================
    // FITUR BARU: EDIT & HAPUS
    // ====================================================

    /**
     * Tampilkan Form Edit (Hanya jika Pending)
     */
    public function edit(Transaction $transaction)
    {
        // 1. Cek Status
        if ($transaction->status !== 'Pending') {
            return redirect()->route('transactions.index')->with('error', 'Transaksi yang sudah diproses tidak dapat diedit.');
        }

        // 2. Cek Kepemilikan (Jika Staff)
        if (Auth::user()->role === 'staff' && $transaction->user_id !== Auth::id()) {
            abort(403, 'Anda hanya dapat mengedit transaksi buatan sendiri.');
        }

        $transaction->load('details');
        
        // Load data untuk dropdown
        if ($transaction->type === 'incoming') {
            $suppliers = Supplier::orderBy('name')->get();
            $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
            // Pastikan Anda membuat view: transactions/edit_incoming.blade.php
            return view('transactions.edit_incoming', compact('transaction', 'suppliers', 'products'));
        } else {
            $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
            // Pastikan Anda membuat view: transactions/edit_outgoing.blade.php
            return view('transactions.edit_outgoing', compact('transaction', 'products'));
        }
    }

    /**
     * Update Transaksi
     */
    public function update(Request $request, Transaction $transaction)
    {
        // 1. Cek Status Lagi
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Tidak bisa mengedit transaksi yang bukan Pending.');
        }

        // 2. Validasi
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
            
            // Cek Stok (Hanya validasi, belum mengurangi)
            foreach ($request->products as $item) {
                $product = Product::find($item['product_id']);
                if ($product->current_stock < $item['quantity']) {
                    return back()->with('error', "Stok tidak cukup untuk {$product->name}. Sisa: {$product->current_stock}");
                }
            }
        }

        DB::beginTransaction();
        try {
            // 3. Update Header Transaksi
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'supplier_id' => $transaction->type === 'incoming' ? $request->supplier_id : null,
                'customer_name' => $transaction->type === 'outgoing' ? $request->customer_name : null,
                'notes' => $request->notes,
            ]);

            // 4. Reset Detail (Hapus lama, buat baru)
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

    /**
     * Hapus Transaksi
     */
    public function destroy(Transaction $transaction)
    {
        // 1. Cek Status
        if ($transaction->status !== 'Pending') {
            return back()->with('error', 'Hanya transaksi dengan status Pending yang dapat dihapus.');
        }

        // 2. Cek Kepemilikan (Jika Staff)
        if (Auth::user()->role === 'staff' && $transaction->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menghapus transaksi ini.');
        }

        DB::beginTransaction();
        try {
            // Hapus detail dulu
            $transaction->details()->delete();
            // Hapus header
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}