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
        Schema::create('payment_verification_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_verification_id');
            $table->foreign('payment_verification_id')->references('id')->on('payment_verifications')->cascadeOnDelete();
            $table->unsignedInteger('debtor_id');
            $table->foreign('debtor_id')->references('id')->on('wa_debtor_trans')->cascadeOnDelete();
            $table->unsignedInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->cascadeOnDelete();
            $table->date('verified_date')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->date('approved_date')->nullable();
            $table->double('amount');
            $table->string('document_no');
            $table->string('reference');
            $table->string('status')->default(PaymentVerification::Pending->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_verification_systems');
    }
};
