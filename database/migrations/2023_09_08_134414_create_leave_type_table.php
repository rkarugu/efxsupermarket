<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_type', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('leave_type');
			$table->string('default_entitlement');
			$table->string('narration');
			$table->enum('recurring', array('On','Off'))->default('Off');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('leave_type');
	}

}
