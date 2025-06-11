<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReversedSalesInvoicesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reversed_sales_invoices', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('sales_invoice_id')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('tyttyuytuhhkuiythgghgh');
			$table->text('sales_invoice_item_id')->nullable();
			$table->float('total_amount', 10)->nullable();
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
		Schema::drop('reversed_sales_invoices');
	}
}
