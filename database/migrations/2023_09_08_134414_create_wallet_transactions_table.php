<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wallet_transactions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('phone_number')->nullable();
			$table->string('entry_type')->nullable();
			$table->decimal('amount', 10)->default(0.00);
			$table->integer('user_id')->unsigned()->nullable()->index('delete_transactions_when_user_is_deleted');
			$table->integer('restaurant_id')->unsigned()->nullable()->index('delete_transactions_when_restro_is_deleted');
			$table->string('refrence_description')->nullable();
			$table->enum('transaction_type', array('CR', 'DR'))->default('CR');
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
		Schema::drop('wallet_transactions');
	}
}
