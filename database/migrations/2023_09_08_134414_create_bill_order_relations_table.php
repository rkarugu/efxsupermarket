<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOrderRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bill_order_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('bill_id')->unsigned()->nullable()->index('deleted_bill_relation_when_bill_is_deleted');
			$table->integer('order_id')->unsigned()->nullable()->index('deleted_bill_relation_when_order_is_deleted');
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
		Schema::drop('bill_order_relations');
	}
}
