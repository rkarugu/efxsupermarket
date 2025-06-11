<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaRecipesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_recipes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->string('title')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('tyttyuytuhhkuiythgghgh');
			$table->string('recipe_number')->nullable();
			$table->integer('major_group_id')->unsigned()->nullable()->index('gdfgdfgdfgdfdsabvcbvc');
			$table->integer('unit_of_mesaurement_id')->unsigned()->nullable()->index('bvmjbkhjghhdhgfhfgh');
			$table->enum('status', array('1', '2'))->default('1');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('delete_recipe_when_delete_location');
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
		Schema::drop('wa_recipes');
	}
}
