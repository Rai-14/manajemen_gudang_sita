<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
 
    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * Tambahkan 'role' dan 'supplier_id'.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // Kolom baru untuk peran pengguna
        'supplier_id',   // Kolom baru untuk peran supplier 
    ];

    /**
     * Kolom yang harus disembunyikan untuk serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kolom yang harus di-casting.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- HELPER METHODS UNTUK ROLE CHECKING ---

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isManager(): bool
    {
        return $this->isRole('manager');
    }

    // Tambahkan helper role lainnya sesuai kebutuhan...
    public function isStaff(): bool
    {
        return $this->isRole('staff');
    }
    public function isAdmin(): bool
    {
        return $this->isRole('admin');
    }
    public function isSupplier(): bool
    {
        return $this->isRole('supplier');
    }

    // --- RELASI ---

    // Relasi ke tabel Supplier (hanya berlaku untuk role 'supplier')
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
