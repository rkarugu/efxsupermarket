<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryOrderBillsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_order_bills', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('deletedeliverybillswhenuserisdeleted');
			$table->string('slug')->nullable();
			$table->string('bill_naration')->nullable();
			$table->enum('status', array('PENDING', 'COMPLETED'))->default('PENDING');
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
		Schema::drop('delivery_order_bills');
	}
}
