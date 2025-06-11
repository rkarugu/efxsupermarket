<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTyreFittingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tyre_fittings', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('vehicle_id')->unsigned()->nullable();
			$table->string('odometer')->nullable();
			$table->integer('number_of_wheels')->nullable();
			$table->integer('tyre_inventory_id')->unsigned()->nullable()->index('tyre_inventory_id');
			$table->string('tyre_make')->nullable();
			$table->string('tyre_size')->nullable();
			$table->string('tyre_type')->nullable();
			$table->string('stock_id_code')->nullable();
			$table->integer('wa_poi_stock_serial_moves_id')->unsigned()->nullable()->index('wa_poi_stock_serial_moves_id');
			$table->integer('serial_no')->nullable();
			$table->integer('trans_type')->nullable();
			$table->string('serial_status')->nullable();
			$table->string('vehicle_register_no')->nullable();
			$table->enum('status', array('1', '0'))->default('1');
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
		Schema::drop('tyre_fittings');
	}
}
