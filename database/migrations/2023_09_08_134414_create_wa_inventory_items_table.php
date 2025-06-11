<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_items', function (Blueprint $table) {
			$table->increments('id');
			$table->string('stock_id_code')->nullable()->index('stock_id_code');
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->text('description')->nullable();
			$table->string('image', 200)->nullable();
			$table->integer('wa_inventory_category_id')->unsigned()->nullable()->index('wii_wit');
			$table->decimal('standard_cost', 10)->nullable()->default(0.00);
			$table->decimal('prev_standard_cost', 10)->default(0.00);
			$table->float('selling_price', 10)->nullable()->default(0.00);
			$table->integer('minimum_order_quantity')->nullable()->default(0);
			$table->integer('wa_unit_of_measure_id')->unsigned()->nullable()->index('wii_wuom');
			$table->integer('tax_manager_id')->unsigned()->nullable()->index('gdfgytujghhdsfr');
			$table->dateTime('cost_update_time')->nullable();
			$table->enum('showroom_stock', array('0', '1'))->default('0');
			$table->enum('new_stock', array('0', '1'))->default('0');
			$table->timestamps();
			$table->integer('pack_size_id')->unsigned()->nullable()->index('pack_size_id');
			$table->integer('store_location_id')->unsigned()->nullable()->index('store_location_id');
			$table->string('alt_code', 100)->nullable();
			$table->string('packaged_volume', 100)->nullable();
			$table->string('gross_weight', 100)->nullable();
			$table->string('net_weight', 100)->nullable();
			$table->boolean('block_this')->nullable()->default(0);
			$table->enum('item_type', array('1', '2'))->default('1')->comment('\'1\'=>maintain item, \'2\'=>maintain raw material');
			$table->float('conversion_rate', 10)->default(0.00);
			$table->boolean('store_c_deleted')->nullable()->default(0);
			$table->boolean('supreme_store_deleted')->default(0);
			$table->string('hs_code')->nullable();
			$table->enum('restocking_method', array('1', '2'))->default('1')->comment('\'1\' => purchasing, \'2\' => production');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_inventory_items');
	}
}
