<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordered_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned()->index('delete_items_when_order_is_deleted');
			$table->integer('food_item_id')->unsigned()->nullable()->index('delete_ordered_item_when_its_item_is_deleted');
			$table->integer('order_offer_id')->unsigned()->nullable()->index('fdsgh_Gfdg_Gdfg');
			$table->integer('restaurant_id')->unsigned()->nullable()->index('deleted_order_item_when_fdss_issss_deleted');
			$table->float('price', 10)->default(0.00);
			$table->string('item_title');
			$table->text('item_comment')->nullable();
			$table->float('item_quantity', 10)->default(0.00);
			$table->text('condiments_json')->nullable();
			$table->text('item_charges')->nullable();
			$table->integer('print_class_id')->unsigned()->nullable()->index('deleted_order_item_when_print_class_issss_deleted');
			$table->enum('item_delivery_status', array('PENDING', 'NEW', 'PREPARATION', 'READY TO PICK', 'DELIVERED', 'CANCLED', 'COMPLETED'))->default('NEW');
			$table->timestamps();
			$table->dateTime('billing_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ordered_items');
	}
}
