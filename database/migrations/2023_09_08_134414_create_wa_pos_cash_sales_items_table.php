<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPosCashSalesItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_pos_cash_sales_items', function (Blueprint $table) {
			$table->bigInteger('id', true);
			$table->integer('wa_pos_cash_sales_id')->unsigned()->nullable()->index('wa_pos_cash_sales_id_from_wa_pos_cash_sales');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id_from_wa_inventory_item');
			$table->decimal('qty', 10, 0)->nullable();
			$table->decimal('selling_price', 20)->nullable();
			$table->decimal('vat_percentage', 10)->nullable();
			$table->decimal('vat_amount', 20)->nullable();
			$table->integer('tax_manager_id')->unsigned()->nullable()->index('tax_manager_id_tax_manager');
			$table->decimal('discount_percent', 20)->nullable();
			$table->decimal('discount_amount', 20)->nullable();
			$table->decimal('total', 20)->nullable();
			$table->timestamps();
			$table->boolean('is_dispatched')->nullable()->default(0);
			$table->integer('dispatched_by')->unsigned()->nullable()->index('dispatched_by');
			$table->dateTime('dispatched_time')->nullable();
			$table->integer('store_location_id')->unsigned()->nullable()->index('store_location_id');
			$table->string('dispatch_no', 250)->nullable();
			$table->boolean('is_return')->nullable()->default(0);
			$table->string('return_grn', 250)->nullable();
			$table->decimal('standard_cost', 20)->nullable()->default(0.00);
			$table->dateTime('return_date')->nullable();
			$table->integer('return_by')->unsigned()->nullable();
			$table->decimal('original_quantity', 20)->nullable();
			$table->decimal('return_quantity', 20)->nullable();
			$table->integer('print_count')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_pos_cash_sales_items');
	}
}
