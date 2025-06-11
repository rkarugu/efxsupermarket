<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDjRequestsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dj_requests', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('delete_request_when_user_is_deleted');
			$table->integer('restaurant_id')->unsigned()->index('delete_request_whern_restro_is_deleted');
			$table->text('comment')->nullable();
			$table->enum('status', array('NEW', 'COMPLETED'))->default('NEW');
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
		Schema::drop('dj_requests');
	}
}
