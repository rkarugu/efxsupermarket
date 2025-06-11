<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryAssignedItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_assigned_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id');
			$table->integer('destination_item_id')->unsigned()->nullable()->index('destination_item_id');
			$table->decimal('conversion_factor', 10)->nullable();
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
		Schema::drop('wa_inventory_assigned_items');
	}
}
