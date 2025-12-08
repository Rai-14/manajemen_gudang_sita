<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'supplier_id',
        'status', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ... sisa method helper role dan relasi tetap sama ...
    public function isRole(string $role): bool { return $this->role === $role; }
    public function isManager(): bool { return $this->isRole('manager'); }
    public function isStaff(): bool { return $this->isRole('staff'); }
    public function isAdmin(): bool { return $this->isRole('admin'); }
    public function isSupplier(): bool { return $this->isRole('supplier'); }
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}