<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // foreignId('category_id') harus dibuat setelah tabel categories
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); 
            $table->string('name');
            $table->string('sku')->unique(); // Stock Keeping Unit - harus unik
            $table->text('description')->nullable();
            $table->decimal('purchase_price', 10, 2); // Harga beli
            $table->decimal('selling_price', 10, 2);  // Harga jual
            $table->unsignedInteger('current_stock'); // Stok saat ini
            $table->unsignedInteger('min_stock');     // Batas stok minimum (untuk alert)
            $table->string('unit'); // pcs, box, kg, dll.
            $table->string('rack_location')->nullable(); // Lokasi rak di gudang
            $table->string('image_path')->nullable(); // Untuk gambar produk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
