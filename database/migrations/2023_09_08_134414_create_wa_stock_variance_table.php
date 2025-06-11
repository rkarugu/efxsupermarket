<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockVarianceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_variance', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('category_code', 250)->nullable();
			$table->string('category_description', 250)->nullable();
			$table->dateTime('batch_date')->nullable();
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
		Schema::drop('wa_stock_variance');
	}
}
