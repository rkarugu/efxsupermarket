<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoyaltyPointsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('loyalty_points', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('order_id')->unsigned()->nullable()->index('deleted_row_when_orderss_is_deleted_d');
			$table->integer('user_id')->unsigned()->nullable()->index('deleted_row_when_user_is_deleted_d');
			$table->integer('wallet_transaction_id')->unsigned()->nullable()->index('fdsfdsfdsf');
			$table->string('points', 10)->nullable();
			$table->enum('points_source', array('ORDER', 'SIGNUP', 'TOPUP'))->default('ORDER');
			$table->enum('status', array('GIVEN', 'PENDING', 'SPENT'))->default('GIVEN');
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
		Schema::drop('loyalty_points');
	}
}
