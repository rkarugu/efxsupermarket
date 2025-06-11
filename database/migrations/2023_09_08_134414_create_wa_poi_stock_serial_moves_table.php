<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPoiStockSerialMovesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_poi_stock_serial_moves', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_stock_move_id')->unsigned()->nullable()->index('wa_poi_stock_serial_moves_wa_stock_move_id_foreign');
			$table->string('serial_no')->nullable();
			$table->decimal('purchase_price', 20)->nullable();
			$table->decimal('purchase_weight', 20)->nullable();
			$table->decimal('value', 20)->nullable();
			$table->string('loc_code')->nullable();
			$table->date('expiration_date')->nullable();
			$table->string('transtype')->nullable();
			$table->timestamps();
			$table->integer('wa_inventory_item_id')->unsigned()->nullable();
			$table->enum('status', array('waiting_retread', 'in_motor_vehicle', 'transit_to_stock', 'emergency', 'damaged', 'new_tyre_in_stock', 'new_but_used', 'retread_but_used', 'in_retread', 'retread_tyre_in_stock', 'transfer'))->nullable();
			$table->integer('vehicle_id')->unsigned()->index('vehicle_id');
			$table->string('odometer')->nullable();
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('tyre_position_id')->unsigned()->nullable()->index('TyrePositionID');
			$table->decimal('retread_cost', 10)->unsigned()->nullable();
			$table->integer('wa_supplier_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_poi_stock_serial_moves');
	}
}
