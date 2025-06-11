<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_order_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('delivery_order_id')->unsigned()->index('deleete_this_whendfs_company_is_deleteddsfs');
			$table->integer('beer_delivery_item_id')->unsigned()->index('deleete_this_when_company_is_deleteddsfs');
			$table->float('price', 10)->default(0.00);
			$table->string('item_title')->nullable();
			$table->text('item_comment')->nullable();
			$table->float('item_quantity', 10)->default(0.00);
			$table->text('item_charges')->nullable();
			$table->enum('item_delivery_status', array('NEW', 'DELIVERED'))->default('NEW');
			$table->dateTime('billing_time')->nullable();
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
		Schema::drop('delivery_order_items');
	}
}
