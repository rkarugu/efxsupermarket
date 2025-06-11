<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAbsentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_absent', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('month')->nullable();
			$table->string('year')->nullable();
			$table->string('absent_days')->nullable();
			$table->enum('active', array('Yes','No'))->default('Yes');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_absent');
	}

}
