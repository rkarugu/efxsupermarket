<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionItemTypesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inspection_item_types', function (Blueprint $table) {
			$table->integer('id')->unsigned();
			$table->string('icon')->nullable();
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->enum('status', array('1', '0'))->default('1');
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
		Schema::drop('inspection_item_types');
	}
}
