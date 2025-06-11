<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockCheckFreezesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_check_freezes', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->text('wa_inventory_category_ids')->nullable();
			$table->timestamps();
			$table->integer('wa_unit_of_measure_id')->unsigned()->nullable()->index('wa_unit_of_measure_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_stock_check_freezes');
	}
}
