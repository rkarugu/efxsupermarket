<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_logs', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->text('request_data')->nullable();
			$table->boolean('process_step')->nullable();
			$table->boolean('status')->default(1);
			$table->timestamps();
			$table->text('required_parameters')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_logs');
	}
}
