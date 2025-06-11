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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->index();
            $table->string('order_no')->index();
            $table->string('invoice_amount');
            $table->string('paid_amount')->nullable();
            $table->string('payment_invoice_no')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('initiating_number')->nullable();
            $table->string('paying_number')->nullable();
            $table->string('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->string('delivery_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
