<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInternalRequisitionDispatchTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_internal_requisition_dispatch', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->string('desp_no', 250)->nullable();
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('wa_internal_requisition_id');
			$table->integer('wa_internal_requisition_item_id')->unsigned()->nullable()->index('wa_internal_requisition_item_id');
			$table->dateTime('dispatched_time')->nullable();
			$table->integer('dispatched_by')->unsigned()->nullable()->index('dispatched_by');
			$table->timestamps();
			$table->decimal('dispatch_quantity', 20)->nullable()->default(0.00);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_internal_requisition_dispatch');
	}
}
