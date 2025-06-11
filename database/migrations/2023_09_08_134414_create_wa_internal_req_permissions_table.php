<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInternalReqPermissionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_internal_req_permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('dfdsfdsfdsfdsfdsfdsfdsfcxzczx');
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('dfdsfdsfdsfdsfdsfdsfdsfcxzczxdf');
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
		Schema::drop('wa_internal_req_permissions');
	}
}
