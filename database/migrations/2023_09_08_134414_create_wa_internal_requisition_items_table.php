<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInternalRequisitionItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_internal_requisition_items', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('fgdfgdfgfddsfdsfdsfdsf');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('fgdfgdfgfddsfdsfdsfdsfdfdsfd');
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('issued_quanity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->integer('tax_manager_id')->unsigned()->nullable()->index('tax_manager_id');
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
			$table->timestamps();
			$table->integer('store_location_id')->unsigned()->nullable();
			$table->decimal('selling_price', 20)->nullable()->default(0.00);
			$table->boolean('is_dispatched')->nullable()->default(0);
			$table->integer('dispatched_by')->unsigned()->nullable()->index('dispatched_by');
			$table->dateTime('dispatched_time')->nullable();
			$table->string('dispatch_no', 250)->nullable();
			$table->string('hs_code', 155)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_internal_requisition_items');
	}
}
