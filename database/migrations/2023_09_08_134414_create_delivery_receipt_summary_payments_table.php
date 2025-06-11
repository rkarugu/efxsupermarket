<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReceiptSummaryPaymentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_receipt_summary_payments', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('delivery_order_receipt_id')->unsigned()->nullable()->index('dfgdfgdfgdfqweewtgjghn');
			$table->string('payment_mode')->nullable();
			$table->float('amount', 10)->default(0.00);
			$table->string('narration')->nullable();
			$table->string('mpesa_request_id')->nullable();
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
		Schema::drop('delivery_receipt_summary_payments');
	}
}
