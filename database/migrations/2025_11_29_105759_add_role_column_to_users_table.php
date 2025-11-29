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
        Schema::table('users', function (Blueprint $table) {
             // Tambahkan kolom role untuk menyimpan peran pengguna
            $table->enum('role', ['admin', 'manager', 'staff', 'supplier'])->default('staff')->after('email');
            
            // Tambahkan kolom supplier_id untuk menghubungkan pengguna (Supplier role) ke tabel suppliers
            // Gunakan 'nullable' karena hanya berlaku untuk role 'supplier'. 
            // Pastikan tabel suppliers sudah ada sebelum migrasi ini
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom saat rollback
            // Harus menghapus foreign key terlebih dahulu
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
            $table->dropColumn('role');
        });
    }
};
