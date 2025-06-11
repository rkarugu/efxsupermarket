<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaNonCashBenefitsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_non_cash_benefits', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('non_cash_benefits_id')->nullable();
			$table->string('year')->nullable();
			$table->string('ref_number')->nullable();
			$table->string('amount')->nullable();
			$table->string('month')->nullable();
			$table->string('narration')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_non_cash_benefits');
	}

}
