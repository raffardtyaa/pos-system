<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // TRX-2025...
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, qris, transfer
            $table->decimal('cash_amount', 15, 2)->nullable(); // Uang yang diterima
            $table->decimal('change_amount', 15, 2)->nullable(); // Kembalian
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};