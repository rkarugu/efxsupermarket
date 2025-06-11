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
        Schema::create('payroll_month_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_month_id');
            $table->unsignedBigInteger('employee_id');
            $table->double('basic_pay')->default(0);
            $table->double('gross_pay')->default(0);
            $table->double('taxable_pay')->default(0);
            $table->double('paye')->default(0);
            $table->double('nssf')->default(0);
            $table->double('shif')->default(0);
            $table->double('housing_levy')->default(0);
            $table->double('net_pay')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_month_details');
    }
};
