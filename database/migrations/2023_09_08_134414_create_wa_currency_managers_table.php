<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCurrencyManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_currency_managers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('ISO4217')->nullable();
			$table->string('slug')->nullable();
			$table->string('country')->nullable();
			$table->integer('decimal_places')->default(0);
			$table->enum('show_in_webshop', array('0', '1'))->nullable();
			$table->decimal('exchange_rate', 10)->default(0.00);
			$table->enum('default_currency', array('1', '0'))->default('0');
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
		Schema::drop('wa_currency_managers');
	}
}
