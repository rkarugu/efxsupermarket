<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCategoryItemPricesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_category_item_prices', function (Blueprint $table) {
			$table->increments('id');
			$table->decimal('price', 10)->nullable();
			$table->integer('item_id')->nullable();
			$table->integer('category_id')->unsigned()->nullable();
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
		Schema::drop('wa_category_item_prices');
	}
}
