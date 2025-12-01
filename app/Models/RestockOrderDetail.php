<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockOrderDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'restock_order_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relasi ke RestockOrder
     */
    public function restockOrder()
    {
        return $this->belongsTo(RestockOrder::class);
    }
    
    /**
     * Relasi ke Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}