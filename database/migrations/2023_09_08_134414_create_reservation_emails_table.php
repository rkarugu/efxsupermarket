<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationEmailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservation_emails', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->string('phone_number', 20)->nullable();
			$table->string('email')->nullable();
			$table->enum('status', array('0', '1'))->default('1');
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
		Schema::drop('reservation_emails');
	}
}
