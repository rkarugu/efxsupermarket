<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEndOfTheDayRoutinesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_end_of_the_day_routines', function (Blueprint $table) {
			$table->increments('id');
			$table->string('routine_no')->nullable();
			$table->date('start_date')->nullable();
			$table->time('open_time')->nullable();
			$table->time('close_time')->nullable();
			$table->enum('status', array('Open', 'Closed'))->default('Open');
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
		Schema::drop('wa_end_of_the_day_routines');
	}
}
