<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage; // Import Storage untuk penanganan file

class ProductController extends Controller

{
    // Batasi akses hanya untuk Admin dan Manager
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');
    }

    /**
     * Menampilkan daftar produk dengan filter, search, dan pagination.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // 1. Logic Search (Berdasarkan Nama atau SKU)
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        // 2. Logic Filter Kategori
        if ($category_id = $request->input('category_id')) {
            $query->where('category_id', $category_id);
        }

        // 3. Logic Filter Status Stok
        if ($stock_status = $request->input('stock_status')) {
            if ($stock_status === 'low_stock') {
                // Gunakan whereColumn untuk membandingkan dua kolom
                $query->whereColumn('current_stock', '<=', 'min_stock');
            } elseif ($stock_status === 'available') {
                $query->whereRaw('current_stock > min_stock'); // Menggunakan whereRaw agar aman
            } elseif ($stock_status === 'out_of_stock') {
                $query->where('current_stock', 0);
            }
        }
        
        // 4. Logic Sorting
        $sort_by = $request->input('sort_by', 'name');
        $sort_direction = $request->input('sort_direction', 'asc');
        $query->orderBy($sort_by, $sort_direction);

        $products = $query->paginate(15)->appends($request->query());

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        // Mengirim semua kategori ke view untuk dropdown
        $categories = Category::all(); 
        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validatedData = $request->validate($this->rules());

        // 2. Penanganan Gambar
        $imagePath = null;
        if ($request->hasFile('image_path')) {
            // Simpan gambar ke storage (misal: storage/app/public/products)
            // Pastikan Anda telah menjalankan 'php artisan storage:link'
            $imagePath = $request->file('image_path')->store('products', 'public');
        }

        // 3. Simpan data ke database
        Product::create(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk.
     */
    public function show(Product $product)
    {
        // ... (Logika detail produk tetap)
        // Kita akan menggunakan relasi yang sudah dibuat di Product Model (belum diimplementasikan)
        // Kita gunakan dummy data atau ambil dari tabel Transactions/TransactionDetails
        $transactions = []; // Akan diimplementasikan di Tahap Transaksi
        
        return view('products.show', compact('product', 'transactions'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Memperbarui produk di database.
     */
    public function update(Request $request, Product $product)
    {
        // Validasi, SKU harus unik kecuali untuk produk ini sendiri
        $validatedData = $request->validate($this->rules($product->id));

        // Penanganan Update Gambar (Opsional)
        $imagePath = $product->image_path; // Default: gunakan path lama
        if ($request->hasFile('image_path')) {
            // Hapus gambar lama jika ada
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image_path')->store('products', 'public');
        }

        // Update produk (kecuali SKU)
        $product->update(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database.
     */
    public function destroy(Product $product)
    {
        // Tambahkan cek stok:
        if ($product->current_stock > 0) {
            return redirect()->route('products.index')->with('error', 'Gagal menghapus! Produk masih memiliki stok (' . $product->current_stock . ' ' . $product->unit . ').');
        }
        
        // Hapus gambar dari storage jika ada
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
    
    /**
     * Aturan validasi untuk store dan update.
     */
    protected function rules($ignoreId = null)
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products')->ignore($ignoreId), // SKU harus unik
            ],
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:purchase_price', // Harga jual harus lebih besar dari harga beli
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'rack_location' => 'nullable|string|max:50',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Aktifkan validasi gambar
        ];
    }
}