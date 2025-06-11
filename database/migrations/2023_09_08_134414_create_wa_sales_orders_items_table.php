<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSalesOrdersItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sales_orders_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->integer('stock_code_id')->unsigned()->nullable()->index('stock_code_id');
			$table->string('stockcode', 250)->nullable();
			$table->string('description', 250)->nullable();
			$table->decimal('price', 20)->nullable();
			$table->string('category_name', 250)->nullable();
			$table->decimal('minimum_price', 20)->nullable();
			$table->decimal('required_qty', 20)->nullable();
			$table->string('comment', 250)->nullable();
			$table->string('order_from', 100)->nullable();
			$table->timestamps();
			$table->integer('wa_sales_orders_id')->unsigned()->nullable()->index('wa_sales_orders_id');
			$table->decimal('total', 20)->nullable()->default(0.00);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_sales_orders_items');
	}
}
