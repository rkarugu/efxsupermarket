<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockVarianceItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_variance_items', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('parent_id')->unsigned()->nullable();
			$table->string('category_code', 250)->nullable();
			$table->string('category_name', 250)->nullable();
			$table->string('uom', 250)->nullable();
			$table->decimal('opening_stock', 20)->nullable();
			$table->decimal('purchase', 20)->nullable();
			$table->decimal('transfers', 20)->nullable();
			$table->decimal('issues', 20)->nullable();
			$table->decimal('total', 20)->nullable();
			$table->decimal('closing_stocks', 20)->nullable();
			$table->decimal('potential_stocks', 20)->nullable();
			$table->decimal('actual_sales', 20)->nullable();
			$table->decimal('variance', 20)->nullable();
			$table->timestamps();
			$table->timestamp('batch_date')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_stock_variance_items');
	}
}
