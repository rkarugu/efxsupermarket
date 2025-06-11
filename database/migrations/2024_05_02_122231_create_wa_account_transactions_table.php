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
        Schema::create('wa_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('transaction_time');
            $table->unsignedInteger('wa_pos_cash_sale_id')->nullable();
            $table->unsignedInteger('account_id')->nullable();
            $table->float('amount')->default(0);
            $table->float('balance')->default(0);
            $table->integer('user_id')->unsigned()->nullable()->index('user_id');
            $table->integer('restaurant_id')->nullable()->index('restaurant_id');
            $table->string('period_number')->nullable();
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_account_transactions');
    }
};
