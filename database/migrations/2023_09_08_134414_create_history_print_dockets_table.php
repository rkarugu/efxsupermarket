<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryPrintDocketsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('history_print_dockets', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('print_class_id')->unsigned()->index('delete_row_when_print_class_is_deleteddd');
			$table->integer('order_id')->unsigned()->index('delete_row_when_order_is_deleteddd');
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
		Schema::drop('history_print_dockets');
	}
}
