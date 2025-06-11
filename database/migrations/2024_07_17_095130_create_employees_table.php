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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gender_id')->constrained();
            $table->foreignId('salutation_id')->nullable()->constrained();
            $table->foreignId('marital_status_id')->constrained();
            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('restaurants');
            $table->unsignedInteger('department_id');
            $table->foreign('department_id')->references('id')->on('wa_departments');
            $table->foreignId('employment_type_id')->constrained();
            $table->foreignId('employment_status_id')->constrained();
            $table->foreignId('job_title_id')->constrained();
            $table->foreignId('job_grade_id')->constrained();
            $table->foreignId('education_level_id')->constrained();
            $table->foreignId('nationality_id')->constrained();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('email');
            $table->string('phone_no');
            $table->string('alternative_phone_no');
            $table->string('id_no');
            $table->string('passport_no')->nullable();
            $table->string('residential_address');
            $table->string('postal_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('image')->nullable();
            $table->string('employee_no');
            $table->string('work_email')->nullable();
            $table->date('employment_date');
            $table->string('pin_no');
            $table->string('nssf_no');
            $table->string('nhif_no');
            $table->string('helb_no');
            $table->double('basic_pay');
            $table->boolean('inclusive_of_house_allowance')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
