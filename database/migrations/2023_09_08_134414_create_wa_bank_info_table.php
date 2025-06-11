<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBankInfoTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bank_info', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('bank_id')->nullable();
			$table->integer('pay_frequency_id')->nullable();
			$table->integer('relief_id')->nullable();
			$table->string('valuntary_nssf')->nullable();
			$table->integer('branch_id')->nullable();
			$table->string('account_name')->nullable();
			$table->string('account_number')->nullable();
			$table->integer('payment_mode_id')->nullable();
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
		Schema::drop('wa_bank_info');
	}
}
