<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackRatingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_ratings', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('feedback_id')->unsigned()->nullable()->index('deleteitwhebnfeedbackisdeleted');
			$table->integer('rating_type_id')->unsigned()->nullable()->index('deleteitwhebnfeedbackisdeletcced');
			$table->float('rating', 10)->nullable();
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
		Schema::drop('feedback_ratings');
	}
}
