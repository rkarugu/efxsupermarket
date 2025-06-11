<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTableAssignmentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_table_assignments', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('delete_table_assignment_when_emp_is_deleted');
			$table->integer('table_manager_id')->unsigned()->index('delete_table_assignment_when_table_is_deleted');
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
		Schema::drop('employee_table_assignments');
	}
}
