<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchLoadedProductsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dispatch_loaded_products', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->integer('salesman_id')->nullable()->index('salesman_id');
			$table->integer('shift_id')->nullable()->index('shift_id');
			$table->integer('store_location_id')->nullable()->index('store_location_id');
			$table->integer('inventory_item_id')->nullable()->index('inventory_item_id');
			$table->decimal('total_qty', 10)->nullable();
			$table->decimal('qty_loaded', 10)->nullable();
			$table->decimal('balance_qty', 10)->nullable();
			$table->boolean('status')->default(1)->comment('1=pending,2=generated');
			$table->boolean('is_requisition_done')->default(0);
			$table->timestamps();
			$table->string('document_no')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dispatch_loaded_products');
	}
}
