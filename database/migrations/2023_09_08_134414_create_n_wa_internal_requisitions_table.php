<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNWaInternalRequisitionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('n_wa_internal_requisitions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('requisition_no')->nullable();
			$table->string('slug');
			$table->integer('user_id')->unsigned()->nullable()->index('dfdsfdsfdsfdssfdsfdsfdsfdsfsdfsd');
			$table->integer('restaurant_id')->unsigned()->index('dfdsfdsfdsfdsfdsfdsfdsfdsfdsfdsfsdfsd');
			$table->integer('wa_department_id')->unsigned()->index('dsfdsfdssfdsfdsfdsfdsfsdfsdg');
			$table->integer('to_store_id')->unsigned()->nullable()->index('to_store_id');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('dsfdsfdssfdsfdsfdsfdsfsdfsdg3')->comment('from location');
			$table->date('requisition_date')->nullable();
			$table->string('vehicle_register_no')->nullable();
			$table->string('route')->nullable();
			$table->string('customer')->nullable();
			$table->enum('status', array('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'COMPLETED'))->default('UNAPPROVED');
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
		Schema::drop('n_wa_internal_requisitions');
	}
}
