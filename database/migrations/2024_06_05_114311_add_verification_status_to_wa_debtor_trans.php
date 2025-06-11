<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->string('verification_status')->default('pending');
            $table->unsignedBigInteger('verification_record_id')->nullable();
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->unsignedBigInteger('matched_debtors_id')->nullable();
            $table->string('channel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verification_record_id']);
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->dropColumn(['matched_debtors_id', 'channel']);
        });
    }
};
