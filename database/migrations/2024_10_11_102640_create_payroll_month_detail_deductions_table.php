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
        Schema::create('payroll_month_detail_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_month_detail_id');
            $table->unsignedBigInteger('deduction_id');
            $table->double('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_month_detail_deductions');
    }
};
