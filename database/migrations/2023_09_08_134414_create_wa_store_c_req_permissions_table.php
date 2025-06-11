<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStoreCReqPermissionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_store_c_req_permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id_users');
			$table->integer('wa_store_c_requisition_id')->unsigned()->nullable()->index('wa_store_c_requisition_id_wa_store_c_requisitions');
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
		Schema::drop('wa_store_c_req_permissions');
	}
}
