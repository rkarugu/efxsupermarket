<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPayrollCommissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_payroll_commission', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('commission_id')->nullable();
			$table->string('year')->nullable();
			$table->string('ref_number')->nullable();
			$table->string('amount')->nullable();
			$table->string('month')->nullable();
			$table->string('narration')->nullable();
			$table->enum('active', array('Yes','No'))->default('No');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_payroll_commission');
	}

}
