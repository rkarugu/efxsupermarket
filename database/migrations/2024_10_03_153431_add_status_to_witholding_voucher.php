<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withholding_payment_vouchers', function (Blueprint $table) {
            $table->boolean('status')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('withholding_payment_vouchers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
