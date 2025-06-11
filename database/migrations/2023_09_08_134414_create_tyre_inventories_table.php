<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTyreInventoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tyre_inventories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('stock_id_code')->nullable();
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->text('description')->nullable();
			$table->string('image', 200)->nullable();
			$table->integer('wa_inventory_category_id')->unsigned()->nullable();
			$table->decimal('standard_cost', 10)->nullable()->default(0.00);
			$table->decimal('prev_standard_cost', 10)->default(0.00);
			$table->float('selling_price', 10)->default(0.00);
			$table->integer('minimum_order_quantity')->nullable()->default(0);
			$table->integer('wa_unit_of_measure_id')->unsigned()->nullable();
			$table->integer('tax_manager_id')->unsigned()->nullable();
			$table->dateTime('cost_update_time')->nullable();
			$table->timestamps();
			$table->enum('inventory_item_type', array('new', 'retread'))->nullable();
			$table->enum('current_obsolete', array('Current', 'Obsolete'))->nullable();
			$table->enum('batch_type', array('No Control', 'Controlled'))->nullable();
			$table->enum('serialised', array('Yes', 'No'))->nullable();
			$table->enum('perishable', array('Yes', 'No'))->nullable();
			$table->decimal('packaged_volume', 20)->nullable();
			$table->decimal('packaged_gross_weight', 20)->nullable();
			$table->decimal('net_weight', 20)->nullable();
			$table->string('tyre_size')->nullable();
			$table->string('tyre_make')->nullable();
			$table->string('pattern')->nullable();
			$table->enum('status', array('waiting_retread', 'in_transit', 'transit_to_stock', 'emergency', 'damaged', 'new_tyre_in_stock', 'new_but_used', 'retread_but_used', 'in_retread'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tyre_inventories');
	}
}
