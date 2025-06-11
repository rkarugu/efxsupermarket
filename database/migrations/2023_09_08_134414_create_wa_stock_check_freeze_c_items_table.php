<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockCheckFreezeCItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_check_freeze_c_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_stock_check_freeze_id')->unsigned()->nullable()->index('wa_stock_check_freeze_id');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('fdsfdsfgmg4543543543d543543');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id');
			$table->integer('item_category_id')->unsigned()->nullable()->index('fdsfdsfgmg4543543543543543');
			$table->decimal('quantity_on_hand', 10, 0)->default(0);
			$table->string('wa_unit_of_measure')->nullable()->index('wa_unit_of_measure_id');
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
		Schema::drop('wa_stock_check_freeze_c_items');
	}
}
