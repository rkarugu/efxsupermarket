<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingTypesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rating_types', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->string('title')->nullable();
			$table->string('image')->nullable();
			$table->enum('rating_for', array('O', 'R'))->nullable();
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
		Schema::drop('rating_types');
	}
}
