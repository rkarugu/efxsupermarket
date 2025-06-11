<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaTyrePurchaseOrderItemControlledsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_tyre_purchase_order_item_controlleds', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_tyre_purchase_order_item_id')->unsigned()->nullable();
			$table->string('serial_no')->nullable();
			$table->decimal('purchase_price', 20)->nullable();
			$table->decimal('purchase_weight', 20)->nullable();
			$table->string('status')->nullable();
			$table->timestamps();
			$table->string('loc_code', 200)->nullable();
			$table->date('expiration_date')->nullable();
			$table->decimal('value', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_tyre_purchase_order_item_controlleds');
	}
}
