<?php

use App\Enums\Status\PaymentVerification;
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
        Schema::create('payment_verification_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_verification_id');
            $table->foreign('payment_verification_id')->references('id')->on('payment_verifications')->cascadeOnDelete();
            $table->string('reference');
            $table->double('amount');
            $table->date('bank_date');
            $table->unsignedBigInteger('verification_system_id')->nullable();
            $table->foreign('verification_system_id')->references('id')->on('payment_verification_systems')->cascadeOnDelete();
            $table->string('status',125)->default(PaymentVerification::Pending->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_verification_banks');
    }
};
