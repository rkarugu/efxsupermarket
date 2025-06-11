<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaExpenseCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_expense_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->nullable();
			$table->integer('expense_id')->nullable();
			$table->integer('tax_manager_id')->nullable();
			$table->string('description')->nullable();
			$table->decimal('amount', 20)->nullable();
			$table->decimal('total', 20)->nullable();
			$table->decimal('tax', 20)->nullable();
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
		Schema::drop('wa_expense_categories');
	}
}
