<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryItemSuppliersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_item_suppliers', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('wa_inventory_item_id')->nullable()->index('wa_inventory_item_id');
			$table->integer('wa_supplier_id')->nullable()->index('wa_supplier_id');
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
		Schema::drop('wa_inventory_item_suppliers');
	}
}
