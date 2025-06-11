<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelentryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fuelentry', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('vehicle')->nullable()->index('vehicle');
			$table->date('fuel_entry_date')->nullable();
			$table->time('fuel_entry_time')->nullable();
			$table->decimal('odometer', 11)->nullable();
			$table->decimal('meter', 11)->nullable();
			$table->decimal('gallons', 11)->nullable();
			$table->decimal('price', 11)->nullable();
			$table->decimal('total', 11)->nullable();
			$table->decimal('fuel_economy', 11)->nullable();
			$table->decimal('cost_per_meter', 11)->nullable();
			$table->string('fuel_type')->nullable();
			$table->integer('vendor_name')->nullable()->index('vendor_name');
			$table->string('reference')->nullable();
			$table->string('flags')->nullable();
			$table->string('photos')->nullable();
			$table->string('documents')->nullable();
			$table->string('comments')->nullable();
			$table->integer('previous_odometer_reading')->unsigned()->nullable();
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
		Schema::drop('fuelentry');
	}
}
