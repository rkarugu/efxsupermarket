<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintClassesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('print_classes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->string('jump_to_status', 100)->nullable();
			$table->enum('can_multiple_print', array('0', '1'))->default('1');
			$table->string('ip')->nullable();
			$table->string('port')->nullable();
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
		Schema::drop('print_classes');
	}
}
