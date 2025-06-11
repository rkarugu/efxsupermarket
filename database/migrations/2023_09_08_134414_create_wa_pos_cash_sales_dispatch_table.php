<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPosCashSalesDispatchTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_pos_cash_sales_dispatch', function (Blueprint $table) {
			$table->increments('id');
			$table->string('desp_no', 250)->nullable();
			$table->integer('pos_sales_id')->unsigned()->nullable()->index('pos_sales_id');
			$table->integer('pos_sales_item_id')->unsigned()->nullable()->index('pos_sales_item_id');
			$table->dateTime('dispatched_time')->nullable();
			$table->integer('dispatched_by')->unsigned()->nullable()->index('dispatched_by');
			$table->timestamps();
			$table->decimal('dispatch_quantity', 20)->nullable()->default(0.00);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_pos_cash_sales_dispatch');
	}
}
