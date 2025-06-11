<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReversedCashSalesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reversed_cash_sales', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('cash_sale_id')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('tyttyuytuhhkuiythgghgh');
			$table->string('cash_sales_item_id')->nullable();
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
		Schema::drop('reversed_cash_sales');
	}
}
