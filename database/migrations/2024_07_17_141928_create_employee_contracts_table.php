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
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('restaurants');
            $table->unsignedInteger('department_id');
            $table->foreign('department_id')->references('id')->on('wa_departments');
            $table->foreignId('employment_type_id')->constrained();
            $table->foreignId('job_title_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
