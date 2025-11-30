<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Konstruktor untuk membatasi akses berdasarkan peran.
     */
    public function __construct()
    {
        // Semua peran kecuali Supplier diizinkan untuk melihat transaksi
        $this->middleware('auth');
        $this->middleware('role:admin,manager,staff');
    }

    /**
     * Menampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        // Logika query transaksi akan diimplementasikan di Langkah 13
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

        $transactions = $query->paginate(15)->appends($request->query());

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru (Incoming/Outgoing).
     * Akan diarahkan ke form spesifik (incoming/outgoing)
     */
    public function create()
    {
        // Hanya Staff yang boleh membuat (Admin/Manager bisa, tapi biasanya melalui Staff)
        if (Auth::user()->isSupplier()) {
            abort(403, 'Akses Ditolak.');
        }

        // Kita bisa membuat form yang mengarahkan ke tipe transaksi
        return view('transactions.create_type'); 
    }

    // Method untuk menampilkan form Barang Masuk
    public function createIncoming()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);
        
        return view('transactions.incoming', compact('suppliers', 'products'));
    }

    // Method untuk menyimpan Transaksi Barang Masuk
    public function storeIncoming(Request $request)
    {
        // Logika Store Incoming akan diimplementasikan di Langkah 15
        return redirect()->route('transactions.index')->with('success', 'Transaksi Barang Masuk berhasil dibuat (Menunggu Verifikasi Manager).');
    }

    // Method untuk menampilkan form Barang Keluar
    public function createOutgoing()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'sku', 'unit', 'current_stock']);

        return view('transactions.outgoing', compact('products'));
    }

    // Method untuk menyimpan Transaksi Barang Keluar
    public function storeOutgoing(Request $request)
    {
        // Logika Store Outgoing akan diimplementasikan di langkah selanjutnya
        return redirect()->route('transactions.index')->with('success', 'Transaksi Barang Keluar berhasil dibuat (Menunggu Approval Manager).');
    }
    
    // ... (method show, edit, update, destroy, dan approval akan ditambahkan) ...
}