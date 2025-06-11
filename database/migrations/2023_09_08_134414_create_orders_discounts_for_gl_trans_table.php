<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersDiscountsForGlTransTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders_discounts_for_gl_trans', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned()->nullable()->index('gfghuwreewh8kutyutyds');
			$table->integer('gl_code_id')->unsigned()->nullable()->index('gfghuwreewh8kutyuty');
			$table->decimal('discount_amount', 10)->default(0.00);
			$table->date('sale_date')->nullable();
			$table->enum('is_posted', array('0', '1'))->default('0');
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
		Schema::drop('orders_discounts_for_gl_trans');
	}
}
