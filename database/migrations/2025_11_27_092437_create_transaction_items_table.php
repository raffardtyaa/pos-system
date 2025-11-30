<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            
            // PERBAIKAN DI SINI: Tambahkan ->nullable() sebelum constrained()
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); 
            
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('price_at_transaction', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};