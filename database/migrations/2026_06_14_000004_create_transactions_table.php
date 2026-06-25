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
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('invoice_number')->unique();
            $table->integer('discount_percent')->default(0);
            $table->bigInteger('discount_amount')->default(0);
            $table->integer('tax_percent')->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('total_price');
            $table->bigInteger('paid_amount');
            $table->bigInteger('change_amount');
            $table->string('payment_method')->default('Cash'); // Cash, QRIS, Transfer, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
