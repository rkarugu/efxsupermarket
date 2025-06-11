<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondimentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('condiments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->string('plu_number')->nullable();
			$table->string('image')->nullable();
			$table->enum('status', array('0', '1'))->default('1');
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
		Schema::drop('condiments');
	}
}
