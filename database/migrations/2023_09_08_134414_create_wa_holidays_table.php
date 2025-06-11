<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_holidays', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('holiday_name')->nullable();
			$table->text('description');
			$table->string('payrate')->nullable();
			$table->date('date')->nullable();
			$table->string('repeats_annually');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_holidays');
	}

}
