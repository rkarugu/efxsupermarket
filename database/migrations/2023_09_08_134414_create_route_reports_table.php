<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRouteReportsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('route_reports', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('report_reason_id');
			$table->integer('route_id')->nullable();
			$table->text('comments')->nullable();
			$table->string('image')->nullable();
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
		Schema::drop('route_reports');
	}
}
