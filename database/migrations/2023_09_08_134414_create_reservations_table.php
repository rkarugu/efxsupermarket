<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('deletd_reservetaion_when_user_is_deleted');
			$table->integer('restaurant_id')->unsigned()->index('deletd_reservetaion_whenrestro_is_deleted');
			$table->dateTime('reservation_time')->nullable();
			$table->string('comment')->nullable();
			$table->string('phone_number', 20)->nullable();
			$table->string('email')->nullable();
			$table->string('event_type')->nullable();
			$table->enum('status', array('CANCLED', 'CONFIRMED', 'NEW'))->default('NEW');
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
		Schema::drop('reservations');
	}
}
