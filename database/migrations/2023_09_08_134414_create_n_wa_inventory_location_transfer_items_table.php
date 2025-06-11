<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNWaInventoryLocationTransferItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('n_wa_inventory_location_transfer_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_inventory_location_transfer_id')->unsigned()->index('dfsdfdsfdsfdsfsdfdssdfdsfsdfds');
			$table->integer('wa_inventory_item_id')->unsigned()->index('dfsdfdsfdsfdsfsdfds');
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->float('vat_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
			$table->timestamps();
			$table->decimal('selling_price', 20)->nullable()->default(0.00);
			$table->decimal('discount_amount', 20)->nullable()->default(0.00);
			$table->boolean('is_return')->nullable()->default(0);
			$table->string('return_grn', 250)->nullable();
			$table->dateTime('return_date')->nullable();
			$table->integer('return_by')->unsigned()->nullable();
			$table->decimal('issued_quantity', 20)->nullable()->default(0.00);
			$table->integer('wa_internal_requisition_item_id')->unsigned()->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('n_wa_inventory_location_transfer_items');
	}
}
