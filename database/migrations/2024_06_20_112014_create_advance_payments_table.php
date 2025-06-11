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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(10001);
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('wa_purchase_order_id');
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('withholding_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Pending', 'Paid'])->default('Pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('prepared_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_payments');
    }
};
