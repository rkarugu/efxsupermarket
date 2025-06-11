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
        Schema::create('payment_voucher_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('payment_voucher_id');
            $table->unsignedInteger('wa_supp_trans_id');
            $table->double('vat_amount');
            $table->double('withholding_amount');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_voucher_items');
    }
};
