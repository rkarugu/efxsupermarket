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
            $table->unsignedInteger('wa_tender_entry_id')->nullable();
            $table->unsignedInteger('cashier_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_payments', function (Blueprint $table) {
            $table->dropColumn('wa_tender_entry_id','cashier_id','branch_id');
        });
    }
};
