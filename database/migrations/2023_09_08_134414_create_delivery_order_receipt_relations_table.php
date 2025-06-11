<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderReceiptRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_order_receipt_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('delivery_order_receipt_id')->unsigned()->nullable()->index('deleterecordswhenreceiptsisdeleted');
			$table->integer('delivery_order_id')->unsigned()->nullable()->index('deleterecordswhfdnreceiptsisdeleted');
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
		Schema::drop('delivery_order_receipt_relations');
	}
}
