<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaiterTipsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('waiter_tips', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('waiter_id')->unsigned()->index('delete_tips_when_waiter_is_deleted');
			$table->integer('order_id')->unsigned()->index('delete_tips_when_waiter_is_ddfdeleted');
			$table->float('tip_amount', 10);
			$table->enum('payment_mode', array('CASH', 'POS', 'MPESA'))->default('CASH');
			$table->string('mpesa_request_id')->nullable();
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
		Schema::drop('waiter_tips');
	}
}
