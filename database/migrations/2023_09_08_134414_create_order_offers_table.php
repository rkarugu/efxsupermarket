<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderOffersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_offers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned()->index('deleted_record_when_order_is_deleted');
			$table->integer('offer_id')->unsigned()->index('deleted_record_when_offer_is_deleted');
			$table->integer('restaurant_id')->unsigned()->nullable();
			$table->string('offer_title')->nullable();
			$table->float('quantity', 10)->default(0.00);
			$table->float('price', 10)->default(0.00);
			$table->text('offer_charges')->nullable();
			$table->timestamps();
			$table->dateTime('billing_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_offers');
	}
}
