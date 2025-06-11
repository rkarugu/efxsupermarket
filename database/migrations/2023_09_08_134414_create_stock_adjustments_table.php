<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockAdjustmentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_adjustments', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('gdfgdfdsadghfjghkjgkhjksdfs');
			$table->string('item_adjustment_code')->nullable();
			$table->integer('item_id')->unsigned()->nullable()->index('gdfgdfdsadghfjghkjgkhjk');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('gdfgdfdsadghfjghkjgkhjksdfsdfs');
			$table->decimal('adjustment_quantity', 10);
			$table->text('comments')->nullable();
			$table->integer('wa_inventory_adjustment_id')->unsigned()->nullable()->index('wa_inventory_adjustment_id');
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
		Schema::drop('stock_adjustments');
	}
}
