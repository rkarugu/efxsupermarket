<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAccountingPeriodsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_accounting_periods', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->string('period_no')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->enum('is_current_period', array('0', '1'));
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
		Schema::drop('wa_accounting_periods');
	}
}
