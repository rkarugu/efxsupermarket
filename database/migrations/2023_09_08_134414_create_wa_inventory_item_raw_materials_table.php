<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryItemRawMaterialsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_item_raw_materials', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_inventory_item_id')->unsigned()->index('wa_inventory_item_raw_materials_wa_inventory_item_id_foreign');
			$table->integer('raw_material_id')->unsigned()->comment('The id column on wa_inventory_items of type Raw Material');
			$table->float('quantity', 10, 0)->comment('The quantity required to make 1 unit of the inventory item.');
			$table->string('notes')->nullable();
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
		Schema::drop('wa_inventory_item_raw_materials');
	}
}
