<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSalesOrderQuotationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sales_order_quotations', function (Blueprint $table) {
			$table->increments('id');
			$table->string('sales_order_number')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_customer_id')->unsigned()->index('fdsfjkjkkuivucivjjjdhfjhdskfh');
			$table->integer('creater_id')->unsigned()->index('fdsfjkjkkuivucivjjjdhfjhdskfhdfdsfds');
			$table->date('order_date')->nullable();
			$table->enum('request_or_delivery', array('request', 'delivery'))->default('request');
			$table->enum('status', array('open', 'close'));
			$table->enum('order_creating_status', array('pending', 'completed'))->default('pending');
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
		Schema::drop('wa_sales_order_quotations');
	}
}
