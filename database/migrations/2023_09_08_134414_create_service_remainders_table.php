<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRemaindersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_remainders', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('vehicle_id')->unsigned()->nullable();
			$table->integer('service_task_id')->unsigned()->nullable();
			$table->string('time_enterval')->nullable();
			$table->string('time_duesoon_threshold')->nullable();
			$table->string('primary_meter_interval')->nullable();
			$table->string('primary_meter_duesoon_threshold')->nullable();
			$table->string('is_manually_due_date')->nullable();
			$table->dateTime('next_due_date')->nullable();
			$table->string('time_enterval_type')->nullable();
			$table->string('time_duesoon_threshold_type')->nullable();
			$table->enum('is_archived', array('0', '1'))->default('0');
			$table->enum('status', array('overdue', 'duesoon', 'upcomming'))->nullable();
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
		Schema::drop('service_remainders');
	}
}
