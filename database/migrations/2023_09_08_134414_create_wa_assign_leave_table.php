<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAssignLeaveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_assign_leave', function(Blueprint $table)
		{
			$table->integer('id', true);
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
			$table->enum('status', array('Pending','Approve','Decline','Complated'))->default('Pending');
			$table->enum('manager_status', array('Pending','Complated','Approve','Decline','Cancelled'))->default('Pending');
			$table->integer('manage_approve_id')->nullable();
			$table->integer('manage_reject_id')->nullable();
			$table->date('manage_approve_date')->nullable();
			$table->date('manage_reject_date')->nullable();
			$table->text('comments')->nullable();
			$table->string('days_applied')->nullable();
			$table->date('date')->nullable();
			$table->string('total_days');
			$table->integer('reject_id')->nullable();
			$table->date('reject_date')->nullable();
			$table->string('opening_blance')->nullable();
			$table->string('accrued')->nullable()->default('0');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_assign_leave');
	}

}
