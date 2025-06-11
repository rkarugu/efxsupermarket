<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwayTakesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('away_takes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->integer('restaurant_id')->unsigned()->index('delete_rows_when_restro_is_deletedddddd');
			$table->string('title')->nullable();
			$table->text('url')->nullable();
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
		Schema::drop('away_takes');
	}
}
