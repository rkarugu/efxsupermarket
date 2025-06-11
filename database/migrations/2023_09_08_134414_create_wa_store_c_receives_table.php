<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStoreCReceivesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_store_c_receives', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->date('date')->nullable();
			$table->time('time')->nullable();
			$table->string('receive_code', 250)->nullable();
			$table->timestamps();
			$table->string('status', 250)->nullable()->default('PENDING');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_store_c_receives');
	}
}
