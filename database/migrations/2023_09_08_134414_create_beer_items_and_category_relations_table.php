<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeerItemsAndCategoryRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beer_items_and_category_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('beer_delivery_item_id')->unsigned()->index('delete_item_when_its_parent_is_deleted');
			$table->integer('beer_keg_category_id')->unsigned()->index('delete_item_when_its_parents_is_deleted');
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
		Schema::drop('beer_items_and_category_relations');
	}
}
