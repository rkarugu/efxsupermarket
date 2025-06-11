<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaTyrePurchaseOrderAuthorizationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_tyre_purchase_order_authorizations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('hjdfdsffgfdgdfg');
			$table->integer('wa_department_id')->unsigned()->index('hjdfdsffgfdgdfgxcxcxcv');
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
		Schema::drop('wa_tyre_purchase_order_authorizations');
	}
}
