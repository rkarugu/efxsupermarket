<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCreditNoteItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_credit_note_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_credit_note_id')->unsigned()->index('wa_credit_note_id_credit_note_items');
			$table->string('item_name')->nullable();
			$table->string('item_no')->nullable();
			$table->decimal('quantity', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->decimal('unit_price', 10)->default(0.00);
			$table->decimal('actual_unit_price', 10)->default(0.00);
			$table->integer('unit_of_measure_id')->unsigned()->nullable()->index('unit_of_measure_id_credit_note_items');
			$table->decimal('total_cost', 10)->default(0.00);
			$table->decimal('vat_rate', 10)->default(0.00);
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->decimal('catering_levy_rate', 10)->default(0.00);
			$table->decimal('catering_levy_amount', 10)->default(0.00);
			$table->decimal('service_charge_rate', 10)->default(0.00);
			$table->decimal('service_charge_amount', 10)->default(0.00);
			$table->decimal('discount_percent', 10)->default(0.00);
			$table->decimal('discount_amount', 10)->default(0.00);
			$table->decimal('total_cost_with_vat', 10)->default(0.00);
			$table->text('note')->nullable();
			$table->enum('item_type', array('item', 'gl-code'))->default('item');
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
		Schema::drop('wa_credit_note_items');
	}
}
