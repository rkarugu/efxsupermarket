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
        Schema::create('banked_drop_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cash_drop_transaction_id')->nullable();
            $table->string('cash_drop_reference')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('customer_reference')->nullable();
            $table->decimal('amount')->default(0);
            $table->timestamp('banked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banked_drop_transactions');
    }
};
