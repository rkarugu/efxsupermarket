<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCustomParameterTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_custom_parameter', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('parameter');
			$table->string('code');
			$table->char('parameter_type');
			$table->enum('recurring', array('On', 'Off', '', ''))->default('Off');
			$table->enum('taxable', array('On', 'Off', '', ''))->default('Off');
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
		Schema::drop('wa_custom_parameter');
	}
}
