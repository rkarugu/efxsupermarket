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
        Schema::create('cash_sale_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamp('transaction_time');
            $table->unsignedInteger('payment_method')->nullable();
            $table->unsignedInteger('wa_pos_cash_sale_id')->nullable();
            $table->unsignedInteger('account_from')->nullable();
            $table->unsignedInteger('account_to')->nullable();
            $table->string('transaction_type')->nullable();
            $table->double('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_sale_entries');
    }
};
