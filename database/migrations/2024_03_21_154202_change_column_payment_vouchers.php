<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->renameColumn('wa_payment_method_id', 'wa_payment_mode_id');
        });
    }


    public function down(): void
    {
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->renameColumn('wa_payment_mode_id', 'wa_payment_method_id');
        });
    }
};
