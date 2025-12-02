<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Import Model Category
use Illuminate\Support\Facades\DB; // Import DB Facade

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsional: Hapus data lama sebelum menambahkan yang baru
        DB::table('categories')->truncate();

        $categories = [
            ['name' => 'Elektronik', 'description' => 'Semua perangkat elektronik dan gadget.'],
            ['name' => 'Peralatan Rumah Tangga', 'description' => 'Barang-barang untuk keperluan rumah tangga.'],
            ['name' => 'Pakaian', 'description' => 'Semua jenis sandang dan tekstil.'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk konsumsi siap jual.'],
            ['name' => 'Suku Cadang', 'description' => 'Komponen atau bagian pengganti.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
        
        // Opsional: Tampilkan notifikasi
        // $this->command->info('Kategori dasar berhasil ditambahkan!');
    }
}