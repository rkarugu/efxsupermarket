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
        Schema::create('billing_supplier_document_processes', function (Blueprint $table) {
            $table->id();
            $table->integer('billing_bank_payment_id')->nullable();
            $table->integer('onboarding_id')->nullable();
            $table->integer('trade_agreement_id')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('bank')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->string('approve_status')->default('Initial');
            $table->dateTime('uploaded_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_supplier_document_processes');
    }
};
