<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type', // 'incoming' atau 'outgoing'
        'user_id', // Staff yang membuat
        'transaction_date',
        'supplier_id', // Hanya untuk incoming
        'customer_name', // Hanya untuk outgoing
        'notes',
        'status', // Pending, Verified, Approved, Shipped, Rejected
    ];

    /**
     * Relasi ke User (Staff yang membuat transaksi)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Supplier (Untuk transaksi barang masuk)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke TransactionDetail (Satu Transaksi memiliki banyak detail produk)
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}