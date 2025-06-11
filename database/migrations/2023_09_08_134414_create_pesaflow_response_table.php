<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesaflowResponseTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pesaflow_response', function (Blueprint $table) {
			$table->increments('id');
			$table->string('payment_reference')->nullable();
			$table->string('currency')->nullable();
			$table->decimal('amount', 10)->nullable();
			$table->date('payment_date')->nullable();
			$table->string('payment_channel')->nullable();
			$table->string('invoice_number')->nullable();
			$table->decimal('invoice_amount', 10)->nullable();
			$table->string('client_invoice_ref')->nullable();
			$table->decimal('amount_paid', 10)->nullable();
			$table->string('status')->nullable();
			$table->text('response')->nullable();
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
		Schema::drop('pesaflow_response');
	}
}
