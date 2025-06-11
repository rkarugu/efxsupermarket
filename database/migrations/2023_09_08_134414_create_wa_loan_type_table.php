<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaLoanTypeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_loan_type', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('loan_type');
			$table->string('interest_method');
			$table->string('annual_interest_rate');
			$table->string('code');
			$table->text('description')->nullable();
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
		Schema::drop('wa_loan_type');
	}
}
