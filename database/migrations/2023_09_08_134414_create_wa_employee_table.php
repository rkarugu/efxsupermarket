<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmployeeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_employee', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('staff_number');
			$table->string('emp_number')->nullable();
			$table->integer('employment_type_id');
			$table->integer('branch_id');
			$table->integer('department_id');
			$table->integer('job_title');
			$table->string('first_name');
			$table->string('middle_name');
			$table->string('last_name');
			$table->string('Id_number');
			$table->string('nhif_no')->nullable();
			$table->string('nssf_no');
			$table->integer('gender_id');
			$table->integer('marital_status');
			$table->date('date_of_birth');
			$table->integer('salutation_id');
			$table->date('date_employed');
			$table->string('cellphone')->nullable();
			$table->string('pin_number');
			$table->integer('job_group_id');
			$table->integer('pay_frequency_id')->nullable();
			$table->integer('bank_id');
			$table->integer('bank_branch');
			$table->string('account_no');
			$table->string('basic_pay')->nullable();
			$table->string('passport_number')->nullable();
			$table->string('driving_license')->nullable();
			$table->date('date_terminated')->nullable();
			$table->integer('years_of_service')->nullable();
			$table->string('ethnicity')->nullable();
			$table->string('pension_no')->nullable();
			$table->string('email_address')->nullable();
			$table->string('postal_address')->nullable();
			$table->string('postal_code', 20)->nullable();
			$table->string('town')->nullable();
			$table->string('country')->nullable();
			$table->text('emp_image')->nullable();
			$table->string('home_phone', 20)->nullable();
			$table->string('home_district')->nullable();
			$table->string('schedule_termination_status')->default('0');
			$table->string('approve_termination')->default('0');
			$table->enum('status', array('Active', 'DeActive'))->default('Active');
			$table->string('sacco_member_no')->nullable();
			$table->string('pension_number')->nullable();
			$table->text('curiculum_vitae')->nullable();
			$table->string('helb_number')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_employee');
	}
}
