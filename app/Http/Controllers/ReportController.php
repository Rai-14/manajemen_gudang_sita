<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Halaman Utama Laporan Inventori
     */
    public function inventory()
    {
        // Pastikan hanya Admin & Manager
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403);
        }

        // 1. Ringkasan Finansial
        $totalItems = Product::sum('current_stock');
        
        // Nilai Aset (Modal) = Stok * Harga Beli
        $totalAssetValue = Product::sum(DB::raw('current_stock * purchase_price'));
        
        // Nilai Jual Potensial = Stok * Harga Jual
        $totalPotentialRevenue = Product::sum(DB::raw('current_stock * selling_price'));
        
        // Estimasi Profit Kotor
        $potentialProfit = $totalPotentialRevenue - $totalAssetValue;

        // 2. Ringkasan Per Kategori
        // Mengambil kategori beserta jumlah produk dan total stok di dalamnya
        $categories = Category::with(['products' => function($query) {
            $query->select('category_id', 'current_stock', 'purchase_price');
        }])->get()->map(function ($category) {
            $category->total_stock = $category->products->sum('current_stock');
            $category->asset_value = $category->products->sum(function($product) {
                return $product->current_stock * $product->purchase_price;
            });
            return $category;
        });

        // 3. Produk Stok Terbanyak & Paling Sedikit (Top 5)
        $topProducts = Product::orderBy('current_stock', 'desc')->take(5)->get();
        $lowProducts = Product::orderBy('current_stock', 'asc')->where('current_stock', '>', 0)->take(5)->get();

        return view('reports.inventory', compact(
            'totalItems', 
            'totalAssetValue', 
            'totalPotentialRevenue', 
            'potentialProfit',
            'categories',
            'topProducts',
            'lowProducts'
        ));
    }

    /**
     * Halaman Cetak (Print Friendly)
     */
    public function printInventory()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isManager()) {
            abort(403);
        }

        $products = Product::with('category')->orderBy('name')->get();
        $totalAssetValue = Product::sum(DB::raw('current_stock * purchase_price'));
        $totalItems = Product::sum('current_stock');

        return view('reports.print_inventory', compact('products', 'totalAssetValue', 'totalItems'));
    }
}