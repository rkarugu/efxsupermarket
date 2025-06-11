<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPayrollPaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_payroll_payment', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('basic_pay')->nullable();
			$table->integer('pay_frequency_id')->nullable();
			$table->integer('branch_id')->nullable();
			$table->string('account_name')->nullable();
			$table->string('account_number')->nullable();
			$table->integer('currency_id')->nullable();
			$table->enum('nhif', array('Off','On'))->nullable()->default('On');
			$table->string('nssf_number', 433)->nullable();
			$table->integer('bank_id')->nullable();
			$table->string('relief')->nullable();
			$table->string('voluntary_nssf')->nullable();
			$table->integer('payment_mode_id')->nullable();
			$table->enum('paye', array('On','Off'))->default('On');
			$table->enum('active', array('Yes','No'))->default('Yes');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_payroll_payment');
	}

}
