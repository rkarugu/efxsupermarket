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
            $table->boolean('reconciled')->default(false);
            $table->boolean('posted')->default(false);
            $table->string('gl_account_name')->nullable();
            $table->unsignedInteger('gl_account_id')->nullable();
            $table->unsignedInteger('balancing_account_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_payments', function (Blueprint $table) {
            $table->dropColumn(['reconciled', 'posted', 'gl_account','balancing_account']);
        });
    }
};
