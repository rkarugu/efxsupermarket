<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodItemsPrintClassRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('food_items_print_class_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('print_class_id')->unsigned()->index('delete_row_when_print_class_is_deleted');
			$table->integer('food_item_id')->unsigned()->index('delete_row_when_food_item_is_deleted');
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
		Schema::drop('food_items_print_class_relations');
	}
}
