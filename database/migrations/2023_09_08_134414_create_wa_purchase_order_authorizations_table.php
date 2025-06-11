<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPurchaseOrderAuthorizationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_purchase_order_authorizations', function (Blueprint $table) {
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
		Schema::drop('wa_purchase_order_authorizations');
	}
}
