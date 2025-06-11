<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWaStoreCRequisitionItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('wa_store_c_requisition_items', function(Blueprint $table)
		{
			$table->foreign('wa_inventory_item_id', 'wa_inventory_item_id_item')->references('id')->on('wa_inventory_items')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('wa_store_c_requisitions_id', 'wa_store_c_requisitions_id_parent')->references('id')->on('wa_store_c_requisitions')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('wa_store_c_requisition_items', function(Blueprint $table)
		{
			$table->dropForeign('wa_inventory_item_id_item');
			$table->dropForeign('wa_store_c_requisitions_id_parent');
		});
	}

}
