<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaGrnsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_grns', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('gdfgdfgdfgdfgdfgdfgdfg');
			$table->integer('wa_purchase_order_item_id')->unsigned()->nullable()->index('gdfgdgfgdfgjghjgjghfdf');
			$table->integer('wa_supplier_id')->unsigned()->nullable()->index('hghgfhgfhfghfghgfhgfghcvbcv');
			$table->string('grn_number')->nullable();
			$table->string('item_code')->nullable();
			$table->date('delivery_date')->nullable();
			$table->string('item_description')->nullable();
			$table->decimal('qty_received', 10)->nullable()->comment('delivered qty');
			$table->decimal('qty_invoiced', 10)->default(0.00)->comment('supplier qty');
			$table->decimal('standart_cost_unit', 10)->default(0.00);
			$table->text('invoice_info')->nullable();
			$table->timestamps();
			$table->enum('return_status', array('Returned', 'Not Returned'))->nullable()->default('Not Returned');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_grns');
	}
}
