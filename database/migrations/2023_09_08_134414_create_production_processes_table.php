<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionProcessesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('production_processes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('operation');
			$table->string('description')->nullable();
			$table->string('notes')->nullable();
			$table->string('status')->default('active')->comment('Options: active,inactive');
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
		Schema::drop('production_processes');
	}
}
