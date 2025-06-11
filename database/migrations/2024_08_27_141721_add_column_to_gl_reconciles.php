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
        Schema::table('gl_reconciles', function (Blueprint $table) {
            $table->date('start_date')->after('ending_balance')->nullable();
        });

        Schema::table('gl_reconcile_interest_expenses', function (Blueprint $table) {
            $table->string('reference')->nullable();
            $table->string('gl_recon_statement_id')->nullable();
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->unsignedInteger('gl_reconcile_id')->nullable();
            $table->unsignedInteger('gl_recon_statement_id')->nullable();
        });

        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->unsignedInteger('gl_reconcile_id')->nullable();
            $table->unsignedInteger('gl_recon_statement_id')->nullable();
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->unsignedInteger('gl_reconcile_id')->nullable();
            $table->unsignedInteger('gl_recon_statement_id')->nullable();
        });

        Schema::table('payment_voucher_cheques', function (Blueprint $table) {
            $table->unsignedInteger('gl_reconcile_id')->nullable();
            $table->unsignedInteger('gl_recon_statement_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gl_reconciles', function (Blueprint $table) {
            $table->dropColumn(['start_date']);
        });

        Schema::table('gl_reconcile_interest_expenses', function (Blueprint $table) {
            $table->dropColumn(['reference','gl_recon_statement_id']);
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->dropColumn(['gl_reconcile_id','gl_recon_statement_id']);
        });

        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->dropColumn(['gl_reconcile_id','gl_recon_statement_id']);
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn(['gl_reconcile_id','gl_recon_statement_id']);
        });

        Schema::table('payment_voucher_cheques', function (Blueprint $table) {
            $table->dropColumn(['gl_reconcile_id','gl_recon_statement_id']);
        });
    }
};
