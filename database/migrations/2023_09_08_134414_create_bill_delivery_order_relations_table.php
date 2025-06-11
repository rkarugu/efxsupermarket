<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillDeliveryOrderRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bill_delivery_order_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('delivery_order_bill_id')->unsigned()->index('dsgfdsfdsfdsfdsfdsfsd');
			$table->integer('delivery_order_id')->unsigned()->index('dsgfdsfdsfdsfdsfdsfsddsf');
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
		Schema::drop('bill_delivery_order_relations');
	}
}
