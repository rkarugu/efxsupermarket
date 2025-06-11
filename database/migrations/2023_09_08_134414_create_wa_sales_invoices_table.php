<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSalesInvoicesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sales_invoices', function (Blueprint $table) {
			$table->increments('id');
			$table->string('sales_invoice_number')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_customer_id')->unsigned()->nullable()->index('fdsfdsfsgdnbnvbnvbnnvb');
			$table->integer('creater_id')->unsigned()->nullable()->index('fdsfdsfsgdnbnvbnvbnnvbdfsdsfds');
			$table->date('order_date')->nullable();
			$table->enum('request_or_delivery', array('request', 'delivery'))->default('request');
			$table->enum('status', array('open', 'close'))->default('open');
			$table->integer('wa_location_and_store_id')->nullable();
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
		Schema::drop('wa_sales_invoices');
	}
}
