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
        Schema::create('customer_equity_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_till');
            $table->unsignedInteger('matched_wa_customer_id')->nullable();
            $table->string('paid_amount');
            $table->string('eb_timestamp');
            $table->string('transaction_reference');
            $table->string('customer_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('matched_route_customer_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_equity_payments');
    }
};
