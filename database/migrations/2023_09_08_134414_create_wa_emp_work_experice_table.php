<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpWorkExpericeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_work_experice', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('company_id')->nullable();
			$table->string('region')->nullable();
			$table->integer('department_id')->nullable();
			$table->integer('job_group_id')->nullable();
			$table->integer('job_grade_id')->nullable();
			$table->string('shift')->nullable();
			$table->string('manager')->nullable();
			$table->integer('employement_status')->nullable();
			$table->date('probation_start_date')->nullable();
			$table->integer('branch_id')->nullable();
			$table->string('station')->nullable();
			$table->string('section')->nullable();
			$table->integer('designation_id')->nullable();
			$table->integer('employement_type_id')->nullable();
			$table->string('home_phone')->nullable();
			$table->string('hod')->nullable();
			$table->date('date_of_confirmation')->nullable();
			$table->date('probation_end_date')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_emp_work_experice');
	}

}
