<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFormsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inspection_forms', function (Blueprint $table) {
			$table->integer('id')->primary();
			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->enum('status', array('1', '0'))->nullable()->default('1')->comment('1=active,0=in active');
			$table->enum('is_archived', array('1', '0'))->nullable()->default('0')->comment('1=archived,0=unarchived');
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
		Schema::drop('inspection_forms');
	}
}
