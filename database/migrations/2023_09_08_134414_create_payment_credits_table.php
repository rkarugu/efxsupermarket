<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCreditsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_credits', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('period')->nullable();
			$table->string('gl_code_id')->nullable();
			$table->string('narration')->nullable();
			$table->string('transaction_type')->nullable();
			$table->string('transaction_no')->nullable();
			$table->decimal('gross_amount', 10)->nullable()->default(0.00);
			$table->decimal('amount', 10)->default(0.00);
			$table->decimal('net_sales', 10)->default(0.00);
			$table->date('date')->nullable();
			$table->enum('type', array('ITEM', 'VAT', 'SERVICETAX', 'CTL'))->nullable();
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
		Schema::drop('payment_credits');
	}
}
