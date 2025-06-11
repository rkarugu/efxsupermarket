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
        Schema::create('cash_drop_transactions', function (Blueprint $table) {
            $table->id();
            $table->float('amount')->default(0);
            $table->float('cashier_balance')->default(0);
            $table->integer('user_id')->nullable();
            $table->integer('cashier_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_drop_transactions');
    }
};
