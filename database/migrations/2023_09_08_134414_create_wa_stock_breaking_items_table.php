<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockBreakingItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_breaking_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_stock_breaking_id')->unsigned()->nullable()->index('wa_stock_breaking_id');
			$table->integer('source_item_id')->unsigned()->nullable()->index('source_item_id');
			$table->decimal('source_item_bal_stock', 20)->nullable()->default(0.00);
			$table->decimal('source_qty', 20)->nullable()->default(0.00);
			$table->integer('destination_item_id')->unsigned()->nullable()->index('destination_item_id');
			$table->decimal('conversion_factor', 20)->nullable()->default(0.00);
			$table->decimal('destination_qty', 20)->nullable()->default(0.00);
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
		Schema::drop('wa_stock_breaking_items');
	}
}
