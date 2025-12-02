<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relasi ke User (Satu Supplier bisa memiliki banyak akun User (role supplier))
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Relasi ke RestockOrder (Satu Supplier memiliki banyak Restock Order)
     */
    public function restockOrders()
    {
        return $this->hasMany(RestockOrder::class);
    }
    
    /**
     * Relasi ke Transaction (Satu Supplier terlibat dalam banyak Transaksi Masuk)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}