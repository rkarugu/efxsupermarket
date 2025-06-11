<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrdersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_orders', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('deletedeliveryorderuserisdeleetd');
			$table->string('slug')->nullable();
			$table->float('order_final_price', 10)->default(0.00);
			$table->text('order_charges')->nullable();
			$table->string('payment_mode')->nullable();
			$table->text('final_comment')->nullable();
			$table->string('transaction_id')->nullable();
			$table->string('mpesa_request_id')->nullable();
			$table->text('address')->nullable();
			$table->enum('status', array('AWAITING_CONFIRMATION_BY_ADMIN', 'CONFIRMED', 'PAID', 'DELIVERED', 'CANCELLED_BY_ADMIN', 'CANCELLED_UNPAID_BY_CUSTOMER'))->default('AWAITING_CONFIRMATION_BY_ADMIN');
			$table->dateTime('billing_time')->nullable();
			$table->dateTime('order_confirm_time')->nullable();
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
		Schema::drop('delivery_orders');
	}
}
