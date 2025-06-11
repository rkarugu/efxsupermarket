<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryLocationTransfersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_location_transfers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('transfer_no')->nullable()->unique('transfer_no');
			$table->string('slug')->nullable();
			$table->date('transfer_date')->nullable();
			$table->string('vehicle_register_no')->nullable();
			$table->string('route')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('gdfgdfgdfgfdgdfgdfgdfgdgfdg');
			$table->integer('shift_id')->nullable();
			$table->integer('restaurant_id')->unsigned()->index('gdfgdfgdfg');
			$table->integer('wa_department_id')->unsigned()->index('gdfgdfgdfgfdgdfgdfgdfgdgdfdfgfdg');
			$table->integer('from_store_location_id')->unsigned()->nullable()->index('dfgdfgdfgdfgdfgdfgd');
			$table->integer('to_store_location_id')->unsigned()->nullable()->index('gdfgdfgdfgfdgdfgdfgdfgd');
			$table->string('customer')->nullable();
			$table->enum('status', array('PENDING', 'COMPLETED'))->default('PENDING');
			$table->integer('route_id')->unsigned()->nullable()->index('route_id');
			$table->integer('customer_id')->unsigned()->nullable()->index('customer_id');
			$table->string('name', 250)->nullable();
			$table->text('upload_data')->nullable();
			$table->integer('print_count')->nullable()->default(0);
			$table->timestamps();
			$table->decimal('customer_discount', 10)->nullable();
			$table->string('customer_pin')->nullable();
			$table->bigInteger('customer_phone_number')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_inventory_location_transfers');
	}
}
