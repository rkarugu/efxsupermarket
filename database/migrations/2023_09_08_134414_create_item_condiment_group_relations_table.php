<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCondimentGroupRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_condiment_group_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('condiment_group_id')->unsigned()->index('delete_this_reltion_when_its_group_is_deleted');
			$table->integer('food_item_id')->unsigned()->index('delete_this_reltion_when_its_food_item_is_deleted');
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
		Schema::drop('item_condiment_group_relations');
	}
}
