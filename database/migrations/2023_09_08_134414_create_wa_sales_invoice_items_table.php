<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSalesInvoiceItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sales_invoice_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_sales_invoice_id')->unsigned()->index('dfgffhhjghjghjghjghjghj');
			$table->string('item_name')->nullable();
			$table->string('item_no')->nullable();
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('unit_price', 10)->default(0.00);
			$table->decimal('actual_unit_price', 10)->default(0.00);
			$table->integer('unit_of_measure_id')->unsigned()->nullable()->index('dfgffhhjghjghjghjghjghjfdsfdsfds');
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->decimal('catering_levy_rate', 10)->default(0.00);
			$table->decimal('catering_levy_amount', 10)->default(0.00);
			$table->decimal('service_charge_rate', 10)->default(0.00);
			$table->decimal('service_charge_amount', 10)->default(0.00);
			$table->decimal('discount_percent', 10)->default(0.00);
			$table->decimal('discount_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
			$table->enum('item_type', array('item', 'gl-code'))->default('item');
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
		Schema::drop('wa_sales_invoice_items');
	}
}
