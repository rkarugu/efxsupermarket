<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitabilitySummaryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profitability_summary', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('route')->nullable();
			$table->decimal('tonnage', 10)->nullable();
			$table->decimal('amount_ratio', 10)->nullable();
			$table->decimal('ctns', 10)->nullable();
			$table->decimal('lines', 10)->nullable();
			$table->time('time_posted')->nullable();
			$table->integer('unmet')->nullable();
			$table->integer('dd_per_week')->nullable();
			$table->integer('travel')->nullable();
			$table->date('date')->nullable();
			$table->enum('status', array('1', '0'))->default('1');
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
		Schema::drop('profitability_summary');
	}
}
