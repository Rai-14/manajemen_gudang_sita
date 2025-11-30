<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'purchase_price',
        'selling_price',
        'current_stock',
        'min_stock',
        'unit',
        'rack_location',
        'image_path', // Jika menggunakan fitur gambar
    ];

    /**
     * Relasi ke Category (Satu produk hanya memiliki satu kategori)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Relasi ke Detail Transaksi (untuk Riwayat Stok)
     */
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Relasi ke Detail Restock Order
     */
    public function restockOrderDetails()
    {
        return $this->hasMany(RestockOrderDetail::class);
    }
    
    /**
     * Helper untuk mengecek apakah stok produk di bawah batas minimum
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }
}