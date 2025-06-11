<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPoiStockSerialMovesHistoryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_poi_stock_serial_moves_history', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('wa_poi_stock_serial_moves_id')->unsigned()->nullable()->index('wa_poi_stock_serial_moves_id');
			$table->string('serial_no')->nullable();
			$table->integer('wa_stock_move_id')->unsigned()->nullable()->index('wa_stock_move_id');
			$table->integer('vehicle_id')->unsigned()->nullable()->index('vehicle_id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->enum('status', array('in_motor_vehicle', 'transit_to_stock', 'emergency', 'damaged', 'new_tyre_in_stock', 'new_but_used', 'retread_but_used', 'waiting_retread', 'in_retread', 'retread_tyre_in_stock', 'transfer'))->default('new_tyre_in_stock');
			$table->timestamps();
			$table->integer('tyre_position_id')->unsigned()->nullable()->index('tyre_position_id');
			$table->decimal('retread_cost', 10)->unsigned()->nullable();
			$table->integer('wa_supplier_id')->nullable()->index('wa_supplier_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_poi_stock_serial_moves_history');
	}
}
