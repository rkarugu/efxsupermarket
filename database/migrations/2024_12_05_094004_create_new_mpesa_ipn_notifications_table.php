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
        Schema::create('new_mpesa_ipn_notifications', function (Blueprint $table) {
            $table->id();
            $table->longText('payment_details');
            $table->string('paybill')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('reference')->nullable();
            $table->double('amount')->nullable();
            $table->string('status')->default('hanging');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_mpesa_ipn_notifications');
    }
};
