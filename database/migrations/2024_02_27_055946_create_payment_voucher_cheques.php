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
        Schema::create('payment_voucher_cheques', function (Blueprint $table) {
            $table->id();
            $table->string('wa_supplier_code');
            $table->unsignedInteger('payment_voucher_id')->nullable();
            $table->string('number')->unique();
            $table->date('date');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_voucher_cheques');
    }
};
