<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionHistoryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inspection_history', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('inspection_form_id')->nullable();
			$table->integer('vehicle_id')->nullable();
			$table->integer('user_id')->nullable();
			$table->string('duration')->nullable();
			$table->text('failed_item')->nullable();
			$table->enum('status', array('1', '0'))->default('1');
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
		Schema::drop('inspection_history');
	}
}
