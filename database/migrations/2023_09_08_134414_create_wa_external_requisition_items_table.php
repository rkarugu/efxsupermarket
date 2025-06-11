<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaExternalRequisitionItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_external_requisition_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_external_requisition_id')->unsigned()->nullable()->index('fdgdfgdgffdsgjghghjgh');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('fdsgjghghjgh');
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
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
		Schema::drop('wa_external_requisition_items');
	}
}
