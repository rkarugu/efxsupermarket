<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaLoanEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_loan_entries', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('loan_type_id')->nullable();
			$table->string('no_of_installments')->nullable();
			$table->string('ref_number')->nullable();
			$table->string('monthly_deduction')->nullable();
			$table->string('amount_applied')->nullable();
			$table->date('date')->nullable();
			$table->string('memo')->nullable();
			$table->enum('active', array('Yes','No'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_loan_entries');
	}

}
