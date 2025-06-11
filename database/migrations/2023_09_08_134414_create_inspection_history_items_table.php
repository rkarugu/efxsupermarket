<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionHistoryItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inspection_history_items', function (Blueprint $table) {
			$table->integer('id');
			$table->integer('inspection_history_id')->unsigned()->nullable();
			$table->integer('inspection_type_id')->unsigned()->nullable();
			$table->integer('inspection_item_id')->unsigned()->nullable();
			$table->text('item_detail')->nullable();
			$table->integer('inspection_form_id')->unsigned()->nullable();
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
		Schema::drop('inspection_history_items');
	}
}
