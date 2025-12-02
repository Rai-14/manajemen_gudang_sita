<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Field yang bisa diisi
    protected $fillable = [
        'name', 
        'description', 
        'image_path'
    ];

    // Relasi ke Produk (satu kategori memiliki banyak produk)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
