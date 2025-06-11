<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeerItemTaxManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beer_item_tax_managers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('beer_delivery_item_id')->unsigned()->index('deleet_when_this_parent_is_deleted');
			$table->integer('tax_manager_id')->unsigned()->index('deleet_when_this_parent_idddds_deleted');
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
		Schema::drop('beer_item_tax_managers');
	}
}
