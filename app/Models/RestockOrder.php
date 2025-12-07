<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import Model Detail agar tidak error
use App\Models\RestockOrderDetail; 

class RestockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'user_id', 
        'order_date',
        'expected_delivery_date',
        'notes',
        'status', 
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Fungsi ini sekarang akan bekerja karena class sudah di-import
    public function details()
    {
        return $this->hasMany(RestockOrderDetail::class, 'restock_order_id');
    }
}