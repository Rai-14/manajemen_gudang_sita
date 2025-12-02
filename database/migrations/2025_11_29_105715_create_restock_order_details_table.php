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
        Schema::create('restock_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restock_order_id')->constrained()->onDelete('cascade'); // Relasi ke restock_orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Relasi ke products
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_order_details');
    }
};
