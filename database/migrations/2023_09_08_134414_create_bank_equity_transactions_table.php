<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankEquityTransactionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bank_equity_transactions', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('billNumber', 250)->nullable();
			$table->decimal('billAmount', 20)->nullable();
			$table->string('CustomerRefNumber', 250)->nullable();
			$table->string('bankreference', 250)->nullable();
			$table->string('tranParticular', 250)->nullable();
			$table->string('paymentMode', 250)->nullable();
			$table->dateTime('transactionDate')->nullable();
			$table->string('phonenumber', 250)->nullable();
			$table->string('debitaccount', 250)->nullable();
			$table->string('debitcustname', 250)->nullable();
			$table->string('transaction_type', 100)->nullable();
			$table->timestamps();
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bank_equity_transactions');
	}
}
