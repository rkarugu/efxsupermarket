<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondimentGroupRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('condiment_group_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('condiment_id')->unsigned()->index('delete_row_when_condiment_is_deleted');
			$table->integer('condiment_group_id')->unsigned()->index('delete_row_when_condiment_groupsis_deleted');
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
		Schema::drop('condiment_group_relations');
	}
}
