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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // Nomor transaksi auto-generated
            $table->enum('type', ['incoming', 'outgoing']); // Tipe: barang masuk atau keluar
            $table->foreignId('user_id')->constrained(); // Staff Gudang yang membuat transaksi
            $table->date('transaction_date');
            
            // Kolom kondisional:
            // Gunakan `constrained('suppliers')` agar foreign key merujuk ke tabel 'suppliers'
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null'); // Untuk 'incoming'

            $table->string('customer_name')->nullable(); // Untuk 'outgoing'
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Verified', 'Approved', 'Shipped', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
