<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductionProcessWaInventoryItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('production_process_wa_inventory_item', function(Blueprint $table)
		{
			$table->foreign('production_process_id', 'ppid_foreign')->references('id')->on('production_processes')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('wa_inventory_item_id', 'process_inventory_item_foreign')->references('id')->on('wa_inventory_items')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('production_process_wa_inventory_item', function(Blueprint $table)
		{
			$table->dropForeign('ppid_foreign');
			$table->dropForeign('process_inventory_item_foreign');
		});
	}

}
