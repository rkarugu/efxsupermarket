<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryAndFoodItemTaxManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_and_food_item_tax_managers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->nullable()->index('delete_when_category_id_is_deleted_from_table');
			$table->integer('food_item_id')->unsigned()->nullable()->index('delete_when_food_item_id_is_deleted_from_table');
			$table->integer('tax_manager_id')->unsigned()->index('delete_whentaxmanager_item_id_is_deleted_from_table');
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
		Schema::drop('category_and_food_item_tax_managers');
	}
}
