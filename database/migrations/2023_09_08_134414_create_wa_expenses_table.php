<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaExpensesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_expenses', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('payee_id')->nullable();
			$table->integer('payment_account_id')->nullable();
			$table->date('payment_date')->nullable();
			$table->integer('payment_method_id')->nullable();
			$table->string('ref_no')->nullable();
			$table->text('memo')->nullable();
			$table->string('attachment')->nullable();
			$table->enum('tax_amount_type', array('Inclusive of Tax', 'Exclusive of Tax', 'Out Of Scope of Tax'))->nullable()->default('Exclusive of Tax');
			$table->boolean('is_processed')->nullable()->default(0);
			$table->integer('restaurant_id')->nullable();
			$table->timestamps();
			$table->decimal('subTotal', 20)->nullable();
			$table->decimal('total', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_expenses');
	}
}
