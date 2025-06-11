<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBankDepositsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bank_deposits', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('payment_account_id')->unsigned()->nullable()->comment('Wa Chart of accounts');
			$table->integer('branch_id')->unsigned()->nullable()->comment('restaurants');
			$table->date('date')->nullable();
			$table->integer('cashback_goes_id')->unsigned()->nullable()->comment('Wa Chart of accounts');
			$table->text('memo')->nullable();
			$table->text('cashback_memo')->nullable();
			$table->decimal('cashback_amount', 20)->nullable()->default(0.00);
			$table->decimal('total', 20)->nullable()->default(0.00);
			$table->decimal('sub_total', 20)->nullable()->default(0.00);
			$table->boolean('is_processed')->nullable()->default(0)->comment('0 = its not processed, 1= processed');
			$table->string('tax_check', 250)->nullable();
			$table->string('receiver_type', 50)->nullable()->default('Customer');
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
		Schema::drop('wa_bank_deposits');
	}
}
