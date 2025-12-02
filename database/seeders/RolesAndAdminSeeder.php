<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run()
    {

        // 1. Buat Dummy Supplier
        $supplier1 = Supplier::create([
            'name' => 'PT. Logistik Jaya',
            'contact_person' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'supplier.jaya@email.com',
            'address' => 'Jl. Industri No. 1, Jakarta'
        ]);
        
        $supplier2 = Supplier::create([
            'name' => 'CV. Alat Gudang Cepat',
            'contact_person' => 'Citra Dewi',
            'phone' => '087654321098',
            'email' => 'supplier.cepat@email.com',
            'address' => 'Jl. Perak No. 5, Surabaya'
        ]);

        // 2. Buat Akun Admin Super
        User::create([
            'name' => 'Admin Gudang Utama',
            'email' => 'admin@gudang.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Buat Akun Peran Lainnya
        User::create([
            'name' => 'Manager Gudang',
            'email' => 'manager@gudang.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Staff Gudang 1',
            'email' => 'staff@gudang.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // 4. Buat Akun Supplier
        User::create([
            'name' => 'Akun Supplier Jaya',
            'email' => 'supplier@gudang.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'supplier_id' => $supplier1->id, // Hubungkan dengan supplier1
        ]);
        
        // Output ke konsol (opsional)
        // $this->command->info('Data peran (Admin, Manager, Staff, Supplier) dan akun awal berhasil dibuat!');
    }
}