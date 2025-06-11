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
        Schema::table('casuals_pay_disbursements', function (Blueprint $table) {
            $table->boolean('expunged')->default(0)->after('call_back_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casuals_pay_disbursements', function (Blueprint $table) {
            $table->dropColumn('expunged');
        });
    }
};
