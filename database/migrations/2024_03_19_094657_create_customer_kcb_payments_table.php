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
        Schema::create('customer_kcb_payments', function (Blueprint $table) {
            $table->id();
            $table->string('document_no');
            $table->string('receiving_till');
            $table->unsignedInteger('matched_wa_customer_id')->nullable();
            $table->double('paid_amount');
            $table->string('kcb_timestamp');
            $table->string('kcb_reference')->nullable();
            $table->string('mpesa_reference')->nullable();
            $table->string('customer_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->unsignedInteger('matched_route_customer_id')->nullable();
            $table->string('status')->default('pending');
            $table->string('narrative')->nullable();
            $table->string('service_provider')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_kcb_payments');
    }
};
