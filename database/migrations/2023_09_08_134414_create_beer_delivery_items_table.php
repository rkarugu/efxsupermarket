<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeerDeliveryItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beer_delivery_items', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->text('description')->nullable();
			$table->float('price', 10)->default(0.00);
			$table->string('image')->nullable();
			$table->string('plu_number')->nullable();
			$table->boolean('status')->default(1);
			$table->enum('is_available_in_stock', array('0', '1'))->default('1');
			$table->enum('is_deleted', array('0', '1'))->default('0');
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
		Schema::drop('beer_delivery_items');
	}
}
