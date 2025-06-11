<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReport2Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('CustomerReport2', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->nullable();
			$table->string('item_name')->nullable();
			$table->decimal('standard_cost', 10)->nullable();
			$table->decimal('unit_price', 10)->nullable();
			$table->decimal('total_cost', 10)->nullable();
			$table->decimal('total_cost_with_vat', 10)->nullable();
			$table->decimal('allocated_amount', 10)->nullable();
			$table->string('sales_invoice_number')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_customer_id')->unsigned()->nullable();
			$table->date('order_date')->nullable();
			$table->enum('request_or_delivery', array('request','delivery'))->nullable();
			$table->decimal('CtAmount', 33)->nullable();
			$table->string('CustomerName')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('CustomerReport2');
	}

}
