<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPettyCashItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_petty_cash_items', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('wa_petty_cash_id')->unsigned()->nullable()->index('wa_petty_cash_id');
			$table->decimal('amount', 20)->nullable();
			$table->string('name', 250)->nullable();
			$table->integer('wa_charts_of_account_id')->unsigned()->nullable()->index('wa_charts_of_account_id');
			$table->string('receive_from', 250)->nullable();
			$table->string('payment_for', 250)->nullable();
			$table->string('collected_by', 250)->nullable();
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
		Schema::drop('wa_petty_cash_items');
	}
}
