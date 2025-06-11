<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWaInventoryItemRawMaterialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('wa_inventory_item_raw_materials', function(Blueprint $table)
		{
			$table->foreign('wa_inventory_item_id')->references('id')->on('wa_inventory_items')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('wa_inventory_item_raw_materials', function(Blueprint $table)
		{
			$table->dropForeign('wa_inventory_item_raw_materials_wa_inventory_item_id_foreign');
		});
	}

}
