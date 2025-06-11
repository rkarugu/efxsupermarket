<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryLocationTransferItemReturnsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_location_transfer_item_returns', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('wa_inventory_location_transfer_item_id')->nullable()->index('wa_inventory_location_transfer_item_id');
			$table->integer('wa_inventory_location_transfer_id')->unsigned()->nullable()->index('wa_inventory_location_transfer_id');
			$table->integer('return_by')->nullable()->index('return_by');
			$table->dateTime('return_date')->nullable();
			$table->string('return_grn', 250)->nullable();
			$table->decimal('return_quantity', 20)->nullable()->default(0.00);
			$table->timestamps();
			$table->integer('print_count')->unsigned()->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_inventory_location_transfer_item_returns');
	}
}
