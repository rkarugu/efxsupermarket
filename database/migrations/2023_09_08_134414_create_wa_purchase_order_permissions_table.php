<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPurchaseOrderPermissionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_purchase_order_permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('fdsfdsfpooroopreiopgf');
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('fdsfdsfpooroopreiopgfdsads');
			$table->integer('approve_level')->nullable();
			$table->text('note')->nullable();
			$table->enum('status', array('NEW', 'HOLD', 'APPROVED', 'DECLINED'))->default('HOLD');
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
		Schema::drop('wa_purchase_order_permissions');
	}
}
