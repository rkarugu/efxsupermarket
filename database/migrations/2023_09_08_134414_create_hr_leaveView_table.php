<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrLeaveViewTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hr_leaveView', function(Blueprint $table)
		{
			$table->integer('id')->nullable();
			$table->integer('emp_id')->nullable();
			$table->date('from')->nullable();
			$table->enum('half_day', array('Yes','No','',''))->nullable();
			$table->string('acting_staff')->nullable();
			$table->string('leave_period')->nullable();
			$table->integer('leave_id')->nullable();
			$table->date('to')->nullable();
			$table->string('leave_balance')->nullable();
			$table->string('purpose')->nullable();
			$table->text('attach_document')->nullable();
			$table->date('date_approved')->nullable();
			$table->integer('approved_by')->nullable();
			$table->string('day_taken')->nullable();
			$table->enum('status', array('Pending','Approve','Decline','Complated'))->nullable();
			$table->enum('manager_status', array('Pending','Complated','Approve','Decline','Cancelled'))->nullable();
			$table->string('FirstName')->nullable();
			$table->string('MiddleName')->nullable();
			$table->string('LastName')->nullable();
			$table->string('emp_number')->nullable();
			$table->string('LeaveType')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hr_leaveView');
	}

}
