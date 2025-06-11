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
        Schema::create('wa_petty_cash_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_petty_cash_request_id')->constrained();
            $table->foreignId('delivery_schedule_id')->nullable()->constrained();
            $table->string('payee_name');
            $table->string('payee_phone_no');
            $table->double('amount');
            $table->longText('payment_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_petty_cash_request_items');
    }
};
