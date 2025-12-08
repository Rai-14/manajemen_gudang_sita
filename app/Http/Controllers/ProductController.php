<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Pastikan Auth diimport

class ProductController extends Controller
{
    // Konstruktor dimatikan. Pengecekan role dipindah ke setiap fungsi.
    // public function __construct() {}

    /**
     * Menampilkan daftar produk (List Products).
     * Semua role (Admin, Manager, Staff) boleh melihat list, tapi Staff view only.
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        // 1. Logic Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Logic Filter Kategori
        if ($category_id = $request->input('category_id')) {
            $query->where('category_id', $category_id);
        }

        // 3. Logic Filter Status Stok
        if ($stock_status = $request->input('stock_status')) {
            if ($stock_status === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'min_stock');
            } elseif ($stock_status === 'available') {
                $query->whereRaw('current_stock > min_stock');
            } elseif ($stock_status === 'out_of_stock') {
                $query->where('current_stock', 0);
            }
        }
        
        // 4. Logic Sorting
        $sort_by = $request->input('sort_by', 'name');
        $sort_direction = $request->input('sort_direction', 'asc');
        
        $allowed_sorts = ['name', 'sku', 'current_stock', 'purchase_price', 'selling_price', 'created_at'];
        if (!in_array($sort_by, $allowed_sorts)) $sort_by = 'name';

        $query->orderBy($sort_by, $sort_direction);

        $products = $query->paginate(5)->appends($request->query());

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Form Tambah Produk.
     * HANYA: Admin & Manager
     */
    public function create()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak. Hanya Admin/Manager.');
        }

        $categories = Category::all(); 
        return view('products.create', compact('categories'));
    }

    /**
     * Simpan Produk Baru.
     * HANYA: Admin & Manager
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak.');
        }

        $validatedData = $request->validate($this->rules());

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('products', 'public');
        }

        Product::create(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Detail Produk.
     * Semua Role Boleh Lihat.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Form Edit Produk.
     * HANYA: Admin & Manager
     */
    public function edit(Product $product)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak.');
        }
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update Produk.
     * HANYA: Admin & Manager
     */
    public function update(Request $request, Product $product)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak.');
        }

        $validatedData = $request->validate($this->rules($product->id));

        $imagePath = $product->image_path;
        if ($request->hasFile('image_path')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image_path')->store('products', 'public');
        }

        $product->update(array_merge($validatedData, [
            'image_path' => $imagePath,
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus Produk.
     * HANYA: Admin & Manager
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403, 'Akses Ditolak.');
        }

        // Validasi: Tidak boleh hapus jika masih ada stok
        if ($product->current_stock > 0) {
            return redirect()->route('products.index')->with('error', 'Gagal menghapus! Produk masih memiliki stok (' . $product->current_stock . ' ' . $product->unit . '). Kosongkan stok terlebih dahulu.');
        }
        
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
    
    protected function rules($ignoreId = null)
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($ignoreId)],
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gt:purchase_price', // Harga Jual > Harga Beli
            'current_stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'rack_location' => 'nullable|string|max:50',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}