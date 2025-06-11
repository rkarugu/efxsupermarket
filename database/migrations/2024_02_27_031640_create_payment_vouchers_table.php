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
        Schema::create('payment_vouchers', function (Blueprint $table) {
            $table->id()->startingValue(10001);
            $table->unsignedInteger('wa_supplier_id');
            $table->unsignedInteger('wa_bank_account_id');
            $table->unsignedInteger('wa_payment_method_id');
            $table->unsignedInteger('status')->default(0);
            $table->double('amount');
            $table->unsignedInteger('prepared_by');
            $table->unsignedInteger('printed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_vouchers');
    }
};
