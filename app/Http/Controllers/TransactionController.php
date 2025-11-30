<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TransactionDetail; // Import model detail
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Pastikan User diimport jika belum

class TransactionController extends Controller
{
    /**
     * Konstruktor untuk membatasi akses berdasarkan peran.
     */
    public function __construct()
    {
        // Semua peran kecuali Supplier diizinkan untuk melihat/mengelola transaksi
        $this->middleware('auth');
        $this->middleware('role:admin,manager,staff');
    }

    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'supplier'])
                            ->latest(); // Urutkan dari yang terbaru

        // Batasi untuk Staff hanya melihat transaksi yang mereka buat
        if (Auth::user()->isStaff()) {
            $query->where('user_id', Auth::id());
        }

        // Filter sederhana berdasarkan tipe (incoming/outgoing)
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
        
        // Filter Status (Tambahan untuk Manager/Admin)
        if ($status = $request->input('status')) {
             if ($status === 'pending_approval' && (Auth::user()->isAdmin() || Auth::user()->isManager())) {
                $query->where('status', 'Pending');
             } else {
                $query->where('status', $status);
             }
        }


        $transactions = $query->paginate(15)->appends($request->query());

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru (Pemilihan Tipe).
     */
    public function create()
    {
        if (Auth::user()->isSupplier()) {
            abort(403, 'Akses Ditolak.');
        }
        return view('transactions.create_type'); 
    }

    /**
     * Method untuk menampilkan form Barang Masuk.
     */
    public function createIncoming()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']); 
        return view('transactions.incoming', compact('suppliers', 'products'));
    }

    /**
     * Method untuk menyimpan Transaksi Barang Masuk (Logika Langkah 15).
     */
    public function storeIncoming(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        try {
            // 2. Buat Nomor Transaksi (Auto-generated: IN/YYMM/XXX)
            $datePart = date('ym');
            $latestTransaction = Transaction::where('type', 'incoming')
                                            ->where('transaction_number', 'like', "IN/{$datePart}/%")
                                            ->latest()
                                            ->first();
            $nextNumber = 1;
            if ($latestTransaction) {
                $lastNumber = (int) substr($latestTransaction->transaction_number, -3);
                $nextNumber = $lastNumber + 1;
            }
            $transactionNumber = "IN/{$datePart}/" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // 3. Simpan Transaksi Utama (Tabel transactions)
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'type' => 'incoming',
                'user_id' => Auth::id(), // Staff yang membuat transaksi
                'transaction_date' => $request->transaction_date,
                'supplier_id' => $request->supplier_id,
                'customer_name' => null, 
                'notes' => $request->notes,
                'status' => 'Pending', // Status awal: Menunggu Verifikasi Manager
            ]);

            $transactionDetails = [];

            // 4. Simpan Detail Transaksi
            foreach ($request->products as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];
                
                if ($quantity <= 0) continue; 
                
                $transactionDetails[] = new TransactionDetail([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
            
            $transaction->details()->saveMany($transactionDetails);

            // 5. Commit Transaksi Database
            DB::commit();

            return redirect()->route('transactions.index')->with('success', "Transaksi Barang Masuk #{$transactionNumber} berhasil dibuat dan menunggu Verifikasi oleh Warehouse Manager.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi. Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Method untuk menampilkan form Barang Keluar.
     */
    public function createOutgoing()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
        return view('transactions.outgoing', compact('products'));
    }

    /**
     * Method untuk menyimpan Transaksi Barang Keluar (Logika Langkah 16).
     */
    public function storeOutgoing(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'transaction_date' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // 1b. Server-side Validation: Pengecekan Stok
        $productIds = collect($request->products)->pluck('product_id')->unique();
        $productsInStock = Product::whereIn('id', $productIds)->pluck('current_stock', 'id');

        foreach ($request->products as $item) {
            $productId = $item['product_id'];
            $quantity = (int) $item['quantity'];
            $availableStock = $productsInStock[$productId] ?? 0;

            if ($quantity > $availableStock) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi! Kuantitas produk melebihi stok tersedia. Stok saat ini: ' . $availableStock);
            }
        }
        
        DB::beginTransaction();
        try {
            // 2. Buat Nomor Transaksi (Auto-generated: OUT/YYMM/XXX)
            $datePart = date('ym');
            $latestTransaction = Transaction::where('type', 'outgoing')
                                            ->where('transaction_number', 'like', "OUT/{$datePart}/%")->latest()->first();
            
            $nextNumber = 1;
            if ($latestTransaction) {
                $lastNumber = (int) substr($latestTransaction->transaction_number, -3);
                $nextNumber = $lastNumber + 1;
            }
            $transactionNumber = "OUT/{$datePart}/" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // 3. Simpan Transaksi Utama (Tabel transactions)
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'type' => 'outgoing',
                'user_id' => Auth::id(), // Staff yang membuat transaksi
                'transaction_date' => $request->transaction_date,
                'supplier_id' => null, // Karena ini Outgoing
                'customer_name' => $request->customer_name, 
                'notes' => $request->notes,
                'status' => 'Pending', // Status awal: Menunggu Approval Manager
            ]);

            $transactionDetails = [];

            // 4. Simpan Detail Transaksi
            foreach ($request->products as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];
                
                if ($quantity <= 0) continue; 
                
                $transactionDetails[] = new TransactionDetail([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
            
            $transaction->details()->saveMany($transactionDetails);

            // 5. Commit Transaksi Database
            DB::commit();

            return redirect()->route('transactions.index')->with('success', "Transaksi Barang Keluar #{$transactionNumber} berhasil dibuat dan menunggu Approval oleh Warehouse Manager.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi. Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    
    // --- BARIS BARU DARI LANGKAH 17 ---

    /**
     * Menampilkan detail transaksi.
     */
    public function show(Transaction $transaction)
    {
        // Pastikan relasi details dimuat bersama produk
        $transaction->load('details.product'); 
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Menyetujui dan memverifikasi transaksi.
     */
    public function approve(Transaction $transaction)
    {
        // 1. Otorisasi dan Status Check
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui transaksi.');
        }
        if ($transaction->status !== 'Pending') {
            return redirect()->back()->with('error', 'Transaksi sudah diproses atau dibatalkan.');
        }
        
        DB::beginTransaction();
        try {
            $updateType = ($transaction->type === 'incoming') ? '+' : '-';
            $success = true;

            // 2. Update Stok dan Detail
            foreach ($transaction->details as $detail) {
                $product = $detail->product;
                $quantity = $detail->quantity;
                
                // Jika Barang Keluar, cek stok lagi (validasi terakhir sebelum update)
                if ($transaction->type === 'outgoing' && $product->current_stock < $quantity) {
                    $success = false;
                    break; // Keluar dari loop jika stok tidak mencukupi
                }

                // Update Stok menggunakan DB::raw
                $product->current_stock = DB::raw("current_stock {$updateType} {$quantity}");
                $product->save();
            }

            if (!$success) {
                DB::rollback();
                return redirect()->back()->with('error', "Gagal menyetujui! Stok salah satu produk tidak mencukupi saat proses approval.");
            }

            // 3. Update Status Transaksi
            $newStatus = ($transaction->type === 'incoming') ? 'Verified' : 'Approved';
            $transaction->status = $newStatus;
            $transaction->save();
            
            DB::commit();

            return redirect()->route('transactions.index')->with('success', "Transaksi #{$transaction->transaction_number} berhasil disetujui dan stok telah diperbarui.");
        
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyetujui transaksi. Kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Menolak transaksi.
     */
    public function reject(Transaction $transaction)
    {
        // 1. Otorisasi dan Status Check
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Anda tidak memiliki izin untuk menolak transaksi.');
        }
        if ($transaction->status !== 'Pending') {
            return redirect()->back()->with('error', 'Transaksi sudah diproses atau dibatalkan.');
        }
        
        // 2. Update Status menjadi Rejected
        $transaction->status = 'Rejected';
        $transaction->save();
        
        return redirect()->route('transactions.index')->with('success', "Transaksi #{$transaction->transaction_number} berhasil ditolak.");
    }
}