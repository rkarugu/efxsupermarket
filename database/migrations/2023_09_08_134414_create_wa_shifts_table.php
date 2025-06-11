<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaShiftsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_shifts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('shift_id')->nullable();
			$table->string('salesman_id')->nullable();
			$table->string('vehicle_register_no')->nullable()->index('wa_customer_id');
			$table->string('route')->nullable();
			$table->string('delivery_note')->nullable();
			$table->string('driver_name')->nullable();
			$table->string('turnmans_name')->nullable();
			$table->enum('status', array('open', 'close'))->default('open');
			$table->date('shift_date')->nullable();
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
		Schema::drop('wa_shifts');
	}
}
