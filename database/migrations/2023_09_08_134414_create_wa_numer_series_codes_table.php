<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaNumerSeriesCodesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_numer_series_codes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('code')->nullable();
			$table->string('module')->nullable();
			$table->string('slug')->nullable();
			$table->text('description')->nullable();
			$table->integer('starting_number')->unsigned()->nullable();
			$table->date('last_date_used')->nullable();
			$table->integer('last_number_used')->unsigned()->nullable();
			$table->integer('type_number')->nullable();
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
		Schema::drop('wa_numer_series_codes');
	}
}
