<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPosCashSalesItemsReturnTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_pos_cash_sales_items_return', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('wa_pos_cash_sales_item_id')->unsigned()->nullable()->index('wa_pos_cash_sales_item_id');
			$table->integer('wa_pos_cash_sales_id')->unsigned()->nullable()->index('wa_pos_cash_sales_id');
			$table->integer('return_by')->unsigned()->nullable()->index('return_by');
			$table->date('return_date')->nullable();
			$table->string('return_grn', 250)->nullable();
			$table->string('return_quantity', 250)->nullable();
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
		Schema::drop('wa_pos_cash_sales_items_return');
	}
}
