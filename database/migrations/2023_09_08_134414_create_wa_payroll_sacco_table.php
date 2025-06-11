<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPayrollSaccoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_payroll_sacco', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('sacco_id')->nullable();
			$table->string('year')->nullable();
			$table->string('ref_number')->nullable();
			$table->string('amount')->nullable();
			$table->string('narration')->nullable();
			$table->string('month')->nullable();
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
		Schema::drop('wa_payroll_sacco');
	}

}
