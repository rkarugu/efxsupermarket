<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaMergedPaymentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_merged_payments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('shift', 200)->nullable();
			$table->integer('salesman_user_id')->unsigned()->nullable()->index('salesman_user_id');
			$table->decimal('amount', 10)->nullable();
			$table->string('payment_account', 100)->nullable();
			$table->string('narration', 200)->nullable();
			$table->string('description', 200)->nullable();
			$table->string('transaction_no', 200)->nullable();
			$table->timestamps();
			$table->integer('shift_id')->unsigned()->nullable()->index('shift_id');
			$table->integer('salesman_id')->unsigned()->nullable()->index('salesman_id');
			$table->boolean('is_cheque_trans')->nullable()->default(0);
			$table->string('check_image', 250)->nullable();
			$table->dateTime('trans_date')->nullable();
			$table->boolean('is_posted_to_account')->nullable()->default(0);
			$table->integer('wa_debtor_trans_id')->unsigned()->nullable()->index('wa_debtor_trans_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_merged_payments');
	}
}
