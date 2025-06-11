<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaDepartmentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_departments', function (Blueprint $table) {
			$table->increments('id');
			$table->string('department_name')->nullable();
			$table->string('slug')->nullable();
			$table->string('department_code')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable();
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
		Schema::drop('wa_departments');
	}
}
