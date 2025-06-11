<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwayTakeHitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('away_take_hits', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('away_take_id')->unsigned()->index('deleted_when_away_take_is_deleted');
			$table->integer('user_id')->unsigned()->index('deleted_when_users_is_deleted');
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
		Schema::drop('away_take_hits');
	}

}