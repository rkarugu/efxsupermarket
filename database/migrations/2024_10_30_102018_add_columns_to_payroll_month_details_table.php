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
        Schema::table('payroll_month_details', function (Blueprint $table) {
            $table->double('tax_relief')->default(0)->after('paye');
            $table->double('insurance_relief')->default(0)->after('tax_relief');
            $table->double('housing_levy_relief')->default(0)->after('insurance_relief');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_month_details', function (Blueprint $table) {
            $table->dropColumn('tax_relief', 'insurance_relief', 'housing_levy_relief');
        });
    }
};
