<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAssetCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_asset_categories', function (Blueprint $table) {
			$table->integer('id')->unsigned();
			$table->string('category_code')->nullable();
			$table->string('category_description')->nullable();
			$table->integer('fixed_asset_id')->unsigned()->nullable();
			$table->integer('profit_loss_depreciation_id')->unsigned()->nullable();
			$table->integer('profit_loss_disposal_id')->unsigned()->nullable();
			$table->integer('balance_sheet_id')->unsigned()->nullable();
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
		Schema::drop('wa_asset_categories');
	}
}
