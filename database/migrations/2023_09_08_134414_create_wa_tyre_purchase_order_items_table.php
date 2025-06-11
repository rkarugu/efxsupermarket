<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaTyrePurchaseOrderItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_tyre_purchase_order_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_tyre_purchase_order_id')->unsigned()->nullable();
			$table->integer('wa_inventory_item_id')->unsigned()->nullable();
			$table->string('item_no')->nullable();
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('prev_standard_cost', 10)->nullable()->default(0.00);
			$table->decimal('order_price', 10)->default(0.00);
			$table->integer('supplier_uom_id')->unsigned()->nullable();
			$table->integer('unit_of_measure')->unsigned()->nullable();
			$table->decimal('supplier_quantity', 10)->default(0.00);
			$table->decimal('unit_conversion', 10)->default(0.00);
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->enum('is_exclusive_vat', array('Yes', 'No'))->default('No');
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->string('round_off', 25)->nullable()->default('0');
			$table->text('note')->nullable();
			$table->enum('item_type', array('Stock', 'Non-Stock'))->nullable()->default('Stock')->comment('Check for item if it is created on Non-stock item');
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
		Schema::drop('wa_tyre_purchase_order_items');
	}
}
