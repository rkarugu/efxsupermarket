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
        Schema::create('equity_ipn_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_amount')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('paybill')->nullable();
            $table->string('customer_Name')->nullable();
            $table->string('customer_Mobile_No')->nullable();
            $table->string('narration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equity_ipn_transactions');
    }
};
