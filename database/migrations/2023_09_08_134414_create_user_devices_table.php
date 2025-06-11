<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDevicesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_devices', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('delete_tokens_when_user_is_deleted');
			$table->enum('device_type', array('ANDROID', 'IPHONE'))->nullable();
			$table->text('device_id');
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
		Schema::drop('user_devices');
	}
}
