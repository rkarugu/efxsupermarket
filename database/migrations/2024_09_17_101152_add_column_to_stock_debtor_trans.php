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
        Schema::table('stock_debtor_trans', function (Blueprint $table) {
            $table->unsignedInteger('stock_debtors_id')->nullable()->change();
            $table->unsignedInteger('stock_non_debtor_id')->nullable();
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->unsignedInteger('stock_debtor_tran_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_debtor_trans', function (Blueprint $table) {
            $table->dropColumn('stock_non_debtor_id');
        });

        Schema::table('payment_verification_banks', function (Blueprint $table) {
            $table->dropColumn('stock_debtor_tran_id');
        });
    }
};
