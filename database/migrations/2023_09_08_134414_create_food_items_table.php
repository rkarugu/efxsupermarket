<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('food_items', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->enum('is_general_item', array('0', '1'))->default('1');
			$table->string('slug');
			$table->text('description')->nullable();
			$table->float('price', 10)->default(0.00);
			$table->decimal('recipe_cost', 10)->default(0.00);
			$table->string('image')->nullable();
			$table->integer('print_class_id')->unsigned()->nullable()->index('delete_item_when_its_printclass_is_deleted');
			$table->integer('wa_recipe_id')->unsigned()->nullable()->index('dfgdfgdfgdfgdfgjkhjkhjkhjk');
			$table->string('plu_number')->nullable();
			$table->boolean('status')->default(1);
			$table->enum('is_available_in_stock', array('0', '1'))->default('1');
			$table->enum('show_to_customer', array('0', '1'))->default('1');
			$table->enum('show_to_waiter', array('0', '1'))->default('1');
			$table->enum('is_deleted', array('0', '1'))->default('0');
			$table->enum('check_stock_before_sale', array('0', '1'))->default('0');
			$table->enum('recipe_mandatory', array('0', '1'))->default('0');
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
		Schema::drop('food_items');
	}
}
