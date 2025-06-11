<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSeparationTermnationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_separation_termnation', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('type_of_termination')->nullable();
			$table->date('termination_date')->nullable();
			$table->date('last_day_worked')->nullable();
			$table->enum('eligible_for_rehire', array('On','Off'))->default('On');
			$table->enum('notice_given', array('On','Off'))->default('On');
			$table->text('reason');
			$table->string('further_detail')->nullable();
			$table->string('notice_period')->nullable();
			$table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_separation_termnation');
	}

}
