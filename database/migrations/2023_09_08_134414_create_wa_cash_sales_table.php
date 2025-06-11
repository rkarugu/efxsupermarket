<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCashSalesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_cash_sales', function (Blueprint $table) {
			$table->increments('id');
			$table->string('cash_sales_number')->nullable();
			$table->integer('shift_id')->nullable()->index('shift_id');
			$table->string('route')->nullable();
			$table->string('vehicle_reg_no')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_customer_id')->unsigned()->nullable()->index('wa_customer_id');
			$table->integer('creater_id')->unsigned()->nullable()->index('fdsfdsfsgdnbnvbnvbnnvbdfsdsfds');
			$table->date('order_date')->nullable();
			$table->enum('request_or_delivery', array('request', 'delivery'))->default('request');
			$table->string('document_no')->nullable();
			$table->enum('status', array('open', 'close'))->default('open');
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
		Schema::drop('wa_cash_sales');
	}
}
