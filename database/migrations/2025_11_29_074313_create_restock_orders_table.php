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
        Schema::create('restock_orders', function (Blueprint $table) {
              $table->id();
            $table->string('po_number')->unique(); // Purchase Order Number - auto generated
            
            // 💡 Change 1: Make the column nullable
            $table->unsignedBigInteger('supplier_id'); 

            // Add the foreign key constraint
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  // 🗑️ Change 2: Remove the redundant ->nullable() on the constraint
                  ->onDelete('cascade'); 
            
            $table->foreignId('user_id')->constrained(); // Manager yang membuat order
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Pending', 'Confirmed by Supplier', 'In Transit', 'Received'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_orders');
    }
};
