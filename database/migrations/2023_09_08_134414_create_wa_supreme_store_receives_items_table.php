<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSupremeStoreReceivesItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_supreme_store_receives_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_supreme_store_receive_id')->unsigned()->nullable()->index('wa_supreme_store_receive_id');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id');
			$table->decimal('current_stock_balance', 20)->nullable()->default(0.00);
			$table->decimal('qty', 20)->nullable()->default(0.00);
			$table->integer('store_location_id')->nullable()->index('store_location_id');
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
		Schema::drop('wa_supreme_store_receives_items');
	}
}
