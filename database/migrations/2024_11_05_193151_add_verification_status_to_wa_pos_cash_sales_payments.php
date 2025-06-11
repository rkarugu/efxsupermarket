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
        Schema::table('wa_pos_cash_sales_payments', function (Blueprint $table) {
            $table->boolean('verified')->default(false);
            $table->string('bank_statement_id')->nullable();
            $table->string('bank_ref')->nullable();
            $table->string('payment_reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_payments', function (Blueprint $table) {
            $table->dropColumn(['verified', 'bank_statement_id', 'bank_ref', 'payment_reference']);
        });
    }
};
