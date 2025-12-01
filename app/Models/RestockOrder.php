<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'user_id', // Manager yang membuat order
        'order_date',
        'expected_delivery_date',
        'notes',
        'status', // Pending, Confirmed by Supplier, In Transit, Received
    ];

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    /**
     * Relasi ke User (Manager)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke RestockOrderDetail
     */
    public function details()
    {
        return $this->hasMany(RestockOrderDetail::class);
    }
}