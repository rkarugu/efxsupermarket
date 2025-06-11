<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCardDetailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_card_details', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('delete_card_details_when_user_deleted');
			$table->string('card_name')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('email')->nullable();
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
		Schema::drop('user_card_details');
	}
}
