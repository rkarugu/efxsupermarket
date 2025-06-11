<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSaccoTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_sacco', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('sacco');
			$table->string('code');
			$table->enum('recurring', array('On', 'Off'))->default('Off');
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
		Schema::drop('wa_sacco');
	}
}
