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
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('marital_status_id')->nullable(true)->change();
            $table->unsignedInteger('branch_id')->nullable(true)->change();
            $table->unsignedInteger('department_id')->nullable(true)->change();
            $table->unsignedBigInteger('employment_type_id')->nullable(true)->change();
            $table->unsignedBigInteger('employment_status_id')->nullable(true)->change();
            $table->unsignedBigInteger('job_title_id')->nullable(true)->change();
            $table->unsignedBigInteger('job_grade_id')->nullable(true)->change();
            $table->unsignedBigInteger('education_level_id')->nullable(true)->change();
            $table->unsignedBigInteger('nationality_id')->nullable(true)->change();
            $table->string('email')->nullable(true)->change();
            $table->string('phone_no')->nullable(true)->change();
            $table->string('alternative_phone_no')->nullable(true)->change();
            $table->string('residential_address')->nullable(true)->change();
            $table->string('employee_no')->nullable(true)->change();
            $table->date('employment_date')->nullable(true)->change();
            $table->string('pin_no')->nullable(true)->change();
            $table->string('nssf_no')->nullable(true)->change();
            $table->string('nhif_no')->nullable(true)->change();
            $table->string('helb_no')->nullable(true)->change();
            $table->double('basic_pay')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('marital_status_id')->nullable(false)->change();
            $table->unsignedInteger('branch_id')->nullable(false)->change();
            $table->unsignedInteger('department_id')->nullable(false)->change();
            $table->unsignedBigInteger('employment_type_id')->nullable(false)->change();
            $table->unsignedBigInteger('employment_status_id')->nullable(false)->change();
            $table->unsignedBigInteger('job_title_id')->nullable(false)->change();
            $table->unsignedBigInteger('job_grade_id')->nullable(false)->change();
            $table->unsignedBigInteger('education_level_id')->nullable(false)->change();
            $table->unsignedBigInteger('nationality_id')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone_no')->nullable(false)->change();
            $table->string('alternative_phone_no')->nullable(false)->change();
            $table->string('residential_address')->nullable(false)->change();
            $table->string('employee_no')->nullable(false)->change();
            $table->date('employment_date')->nullable(false)->change();
            $table->string('pin_no')->nullable(false)->change();
            $table->string('nssf_no')->nullable(false)->change();
            $table->string('nhif_no')->nullable(false)->change();
            $table->string('helb_no')->nullable(false)->change();
            $table->double('basic_pay')->nullable(false)->change();
        });
    }
};
