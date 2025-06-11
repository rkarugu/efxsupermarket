<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderSaleRepRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_order_sale_rep_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('delivery_order_id')->unsigned()->index('deleeteitwheneorderisdeleted');
			$table->integer('representative_id')->unsigned()->index('deleeteitwhenesalerepresentativeisdeleted');
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
		Schema::drop('delivery_order_sale_rep_relations');
	}
}
