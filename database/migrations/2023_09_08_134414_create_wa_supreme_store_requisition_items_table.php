<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSupremeStoreRequisitionItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_supreme_store_requisition_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_supreme_store_requisitions_id')->unsigned()->nullable()->index('wa_supreme_store_requisitions_id_parent');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id_item');
			$table->decimal('quantity', 10)->nullable()->default(0.00);
			$table->decimal('issued_quanity', 10)->nullable()->default(0.00);
			$table->decimal('standard_cost', 10)->nullable()->default(0.00);
			$table->decimal('total_cost', 10)->nullable()->default(0.00);
			$table->decimal('vat_rate', 10)->nullable()->default(0.00);
			$table->decimal('vat_amount', 10)->nullable()->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->nullable()->default(0.00);
			$table->text('note')->nullable();
			$table->timestamps();
			$table->integer('store_location_id')->nullable()->index('store_location_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_supreme_store_requisition_items');
	}
}
