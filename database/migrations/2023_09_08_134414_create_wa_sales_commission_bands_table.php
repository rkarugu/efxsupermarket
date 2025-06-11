<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSalesCommissionBandsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sales_commission_bands', function (Blueprint $table) {
			$table->increments('id');
			$table->string('sales_from')->nullable();
			$table->string('sales_to')->nullable();
			$table->string('amount')->nullable();
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
		Schema::drop('wa_sales_commission_bands');
	}
}
