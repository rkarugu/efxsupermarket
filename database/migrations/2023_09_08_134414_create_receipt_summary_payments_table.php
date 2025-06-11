<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptSummaryPaymentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('receipt_summary_payments', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_receipt_id')->unsigned()->nullable()->index('delete_payment_summary_when_receipt_is_deleted');
			$table->string('payment_mode')->nullable();
			$table->float('amount', 10)->default(0.00);
			$table->string('narration')->nullable();
			$table->string('mpesa_request_id')->nullable();
			$table->integer('restaurant_id')->nullable();
			$table->enum('category_of_complimentary', array('1', '2', '3'))->nullable();
			$table->date('shift_date')->nullable();
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
		Schema::drop('receipt_summary_payments');
	}
}
