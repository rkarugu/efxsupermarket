<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryAdjustmentTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_adjustment', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('document_no', 200)->nullable();
			$table->integer('status')->nullable()->default(0);
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
		Schema::drop('wa_inventory_adjustment');
	}
}
