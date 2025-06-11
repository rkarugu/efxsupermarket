<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_voucher_items', function (Blueprint $table) {
            $table->string('payable_type')->after('payment_voucher_id')->default('invoice');
            $table->renameColumn('wa_supp_trans_id', 'payable_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_voucher_items', function (Blueprint $table) {
            $table->dropColumn('payable_type');
            $table->renameColumn('payable_id', 'wa_supp_trans_id');
        });
    }
};
