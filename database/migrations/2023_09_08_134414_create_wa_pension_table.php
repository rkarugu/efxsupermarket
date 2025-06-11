<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPensionTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_pension', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('pension');
			$table->string('code');
			$table->string('rate');
			$table->enum('use_rate', array('On', 'Off'))->default('Off');
			$table->enum('recurring', array('On', 'Off'))->default('Off');
			$table->enum('taxable', array('On', 'Off'))->default('Off');
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
		Schema::drop('wa_pension');
	}
}
