<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('delete_order_when_its_user_is_deleted');
			$table->string('slug')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable()->index('delete_order_when_restro_is_deleted');
			$table->text('final_comment')->nullable();
			$table->integer('total_guests')->default(1);
			$table->enum('status', array('NEW_ORDER', 'DELIVERED', 'CANCLED', 'COMPLETED', 'PENDING'))->default('NEW_ORDER');
			$table->float('order_final_price', 10)->default(0.00);
			$table->text('order_charges')->nullable();
			$table->string('payment_mode')->nullable();
			$table->text('order_discounts')->nullable();
			$table->enum('order_type', array('PREPAID', 'POSTPAID'))->default('POSTPAID');
			$table->string('complimentry_code')->nullable();
			$table->text('compliementary_reason')->nullable();
			$table->float('admin_discount_in_percent', 10)->default(0.00)->comment('authorized person can give discount for better orders when they have in postpad conditions');
			$table->integer('discounting_user_id')->unsigned()->nullable()->index('delete_order_when_its_discounting_useris_deleted');
			$table->text('discount_reason')->nullable();
			$table->string('mpesa_request_id')->nullable();
			$table->string('transaction_id')->nullable();
			$table->timestamps();
			$table->dateTime('billing_time')->nullable();
			$table->integer('order_canceled_by_user')->unsigned()->nullable()->index('delete_order_when_cancled_order_user_is_deleted');
			$table->integer('order_canceled_by_print_class_user')->unsigned()->nullable();
			$table->text('order_cancle_reason')->nullable();
			$table->enum('category_of_complimentary', array('1', '2', '3'))->nullable()->comment('1-Staff Complimentary, 2- Guest Complimentary, 3-Pending Bills');
			$table->integer('complimentary_user_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}
}
