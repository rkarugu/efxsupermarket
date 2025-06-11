<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensehistoryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expensehistory', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('vehicle', 11)->nullable();
			$table->string('expense_type')->nullable();
			$table->string('vendor', 11)->nullable();
			$table->float('amount', 11)->nullable();
			$table->string('frequency')->nullable();
			$table->date('date')->nullable();
			$table->string('notes')->nullable();
			$table->string('photos')->nullable();
			$table->string('documents')->nullable();
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
		Schema::drop('expensehistory');
	}
}
