<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceIssuesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_issues', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('service_task')->nullable();
			$table->integer('servicehistory_id');
			$table->string('parts')->nullable();
			$table->string('labor')->nullable();
			$table->string('subtotal')->nullable();
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
		Schema::drop('service_issues');
	}
}
