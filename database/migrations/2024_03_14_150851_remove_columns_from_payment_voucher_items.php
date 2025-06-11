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
        Schema::table('payment_voucher_items', function (Blueprint $table) {
            $table->dropColumn('vat_amount');
            $table->dropColumn('withholding_amount');
            $table->dropColumn('total_amount_inc_vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_voucher_items', function (Blueprint $table) {
            $table->double('vat_amount')->default(0);
            $table->double('withholding_amount')->default(0);
            $table->double('total_amount_inc_vat')->default(0);
        });
    }
};
