<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPayrollLoanTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_payroll_loan_type', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('loan_type_id')->nullable();
			$table->string('year')->nullable();
			$table->string('monthly_deduction')->nullable();
			$table->enum('active', array('Yes','No'))->default('Yes');
			$table->string('principal_deducted')->nullable();
			$table->string('month')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_payroll_loan_type');
	}

}
