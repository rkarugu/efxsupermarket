<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issues', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('asset')->nullable();
			$table->date('reported_date')->nullable();
			$table->time('time')->nullable();
			$table->string('summary')->nullable();
			$table->string('description')->nullable();
			$table->string('reported_by')->nullable();
			$table->string('assigned')->nullable();
			$table->date('due_date')->nullable();
			$table->string('photos')->nullable();
			$table->string('documents')->nullable();
			// $table->simple_array('resolve')->nullable()->default('open');
			$table->boolean('status')->default(0);
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
		Schema::drop('issues');
	}
}
