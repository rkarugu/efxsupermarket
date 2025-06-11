<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaApproveTerminationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_approve_termination', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('type_of_termination')->nullable();
			$table->date('termination_date')->nullable();
			$table->date('last_day_worked')->nullable();
			$table->enum('eligible_for_rehire', array('On','Off','',''))->default('On');
			$table->text('notice_period')->nullable();
			$table->text('reason')->nullable();
			$table->text('comment')->nullable();
			$table->enum('notice_given', array('On','Off','',''))->default('On');
			$table->enum('cleared', array('On','Off','',''))->default('On');
			$table->text('termination_letter')->nullable();
			$table->text('termination_clearance')->nullable();
			$table->text('termination_service')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_approve_termination');
	}

}
