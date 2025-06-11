<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReceiptRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_receipt_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_receipt_id')->unsigned()->nullable()->index('deleted_when_receipt_is_deleted');
			$table->integer('order_id')->unsigned()->nullable()->index('deleted_when_receipt_is_deleted___');
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
		Schema::drop('order_receipt_relations');
	}
}
