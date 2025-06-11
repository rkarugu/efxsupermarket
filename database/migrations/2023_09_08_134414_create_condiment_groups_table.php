<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondimentGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('condiment_groups', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->integer('max_selection_limit')->nullable();
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
		Schema::drop('condiment_groups');
	}
}
