<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryLocationTransferItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_location_transfer_items', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('wa_inventory_location_transfer_id')->unsigned()->index('dfsdfdsfdsfdsfsdfdssdfdsfsdfds');
			$table->integer('wa_inventory_item_id')->unsigned()->index('dfsdfdsfdsfdsfsdfds');
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->integer('tax_manager_id')->unsigned()->nullable()->index('tax_manager_id');
			$table->float('vat_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
			$table->integer('store_location_id')->unsigned()->nullable()->index('store_location_id');
			$table->integer('to_store_location_id')->unsigned()->nullable()->index('to_store_location_id');
			$table->timestamps();
			$table->decimal('selling_price', 20)->nullable()->default(0.00);
			$table->decimal('discount_amount', 20)->nullable()->default(0.00);
			$table->boolean('is_return')->nullable()->default(0);
			$table->string('return_grn', 250)->nullable();
			$table->dateTime('return_date')->nullable();
			$table->integer('return_by')->unsigned()->nullable()->index('return_by');
			$table->decimal('issued_quantity', 20)->nullable()->default(0.00);
			$table->integer('wa_internal_requisition_item_id')->unsigned()->nullable()->index('wa_internal_requisition_item_id');
			$table->decimal('return_quantity', 20)->nullable()->default(0.00);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_inventory_location_transfer_items');
	}
}
