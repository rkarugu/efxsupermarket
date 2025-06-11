<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbacksTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedbacks', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable();
			$table->integer('order_id')->unsigned()->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable();
			$table->text('feedback')->nullable();
			$table->enum('status', array('N', 'Y'))->default('Y')->comment('Y for feedback given N for skipped');
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
		Schema::drop('feedbacks');
	}
}
