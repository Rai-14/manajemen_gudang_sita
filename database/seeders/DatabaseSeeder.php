<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Supplier Dummy
        $supplier = Supplier::create([
            'name' => 'PT. Elektronik Jaya',
            'email' => 'contact@elektronikjaya.com',
            'phone' => '08123456789',
            'address' => 'Jl. Industri No. 1, Jakarta',
        ]);

        // 2. Buat Akun Users untuk setiap Role
        
        // Admin
        User::create([
            'name' => 'Admin Gudang',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Warehouse Manager
        User::create([
            'name' => 'Budi Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Staff Gudang
        User::create([
            'name' => 'Siti Staff',
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Supplier User (Link ke Supplier ID di atas)
        User::create([
            'name' => 'Sales Supplier',
            'email' => 'supplier@test.com',
            'password' => Hash::make('password'),
            'role' => 'supplier',
            'supplier_id' => $supplier->id,
        ]);

        // 3. Buat Kategori Dummy
        $cat1 = Category::create(['name' => 'Elektronik', 'description' => 'Barang elektronik']);
        $cat2 = Category::create(['name' => 'Furniture', 'description' => 'Perabot rumah']);

        // 4. Buat Produk Dummy
        Product::create([
            'category_id' => $cat1->id,
            'name' => 'Laptop Asus ROG',
            'sku' => 'LAP-001',
            'purchase_price' => 15000000,
            'selling_price' => 18000000,
            'current_stock' => 50,
            'min_stock' => 5,
            'unit' => 'Unit',
        ]);
        
        Product::create([
            'category_id' => $cat1->id,
            'name' => 'Mouse Logitech',
            'sku' => 'ACC-002',
            'purchase_price' => 50000,
            'selling_price' => 100000,
            'current_stock' => 2, // Low stock simulation
            'min_stock' => 10,
            'unit' => 'Pcs',
        ]);
    }
}