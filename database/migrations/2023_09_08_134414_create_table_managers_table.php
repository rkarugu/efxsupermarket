<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('table_managers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->string('block_section')->nullable();
			$table->integer('capacity')->nullable()->default(0);
			$table->integer('restaurant_id')->unsigned()->nullable()->index('delete_table_when_its_restaurents_delete');
			$table->enum('status', array('0', '1'))->default('0');
			$table->enum('booking_status', array('FREE', 'BOOKED', 'BLOCKED'))->default('FREE');
			$table->integer('booking_for_user_id')->unsigned()->nullable();
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
		Schema::drop('table_managers');
	}
}
