<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSoldButUnbookedItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sold_but_unbooked_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned()->nullable()->index('hfghfdhfghfghffdsffdfghgdssdfhgfhdfs');
			$table->integer('wa_recipe_ingredient_id')->unsigned()->nullable()->index('hfghfdhfg645gfdfds5gdghgdssdfhgfhdfs');
			$table->integer('ordered_item_id')->unsigned()->nullable()->index('hfghfdhfghfghffdsffdfghgfhgfhdfs');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('hfghfdhfg645gfdgdghgdssdfhgfhdfs');
			$table->decimal('qoh', 10)->default(0.00);
			$table->decimal('deficient_quantity', 10)->default(0.00);
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
		Schema::drop('wa_sold_but_unbooked_items');
	}
}
