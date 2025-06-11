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
        Schema::create('banked_cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('chief_cashier_declaration_id')->nullable();
            $table->string('chief_cashier_declaration_reference')->nullable();
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
        Schema::dropIfExists('banked_cash_transactions');
    }
};
