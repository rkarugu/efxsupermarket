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
        Schema::table('wa_company_preferences', function (Blueprint $table) {
            $table->integer('cash_sales_control_account')->unsigned()->nullable()->index('sasasasadsdsssfdqdyx');
            $table->integer('sales_control_account')->unsigned()->nullable()->index('sasasasadsdsssfdqydyx');
            $table->integer('vat_control_account')->unsigned()->nullable()->index('sasasasadsdsssfduiqdyx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_company_preferences', function (Blueprint $table) {
            //
        });
    }
};
