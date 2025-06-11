<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollCustomDeductionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payroll_custom_deduction', function(Blueprint $table)
		{
			$table->integer('id')->nullable();
			$table->integer('emp_id')->nullable();
			$table->integer('custom_parameters_id')->nullable();
			$table->string('year')->nullable();
			$table->string('ref_number')->nullable();
			$table->string('amount')->nullable();
			$table->string('narration')->nullable();
			$table->string('month')->nullable();
			$table->enum('active', array('Yes','No'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payroll_custom_deduction');
	}

}
