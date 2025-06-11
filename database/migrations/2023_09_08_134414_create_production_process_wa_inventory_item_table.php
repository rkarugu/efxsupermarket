<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionProcessWaInventoryItemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('production_process_wa_inventory_item', function(Blueprint $table)
		{
			$table->integer('production_process_id')->unsigned()->index('ppid_foreign');
			$table->integer('wa_inventory_item_id')->unsigned()->index('process_inventory_item_foreign');
			$table->float('duration', 10, 0)->comment('In minutes');
			$table->boolean('quality_control_check')->default(0);

            // Added in another migration
            // $table->unsignedInteger('step_number')->nullable()->comment('Unique for process-item combination');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('production_process_wa_inventory_item');
	}

}
