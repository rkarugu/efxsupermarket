<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAssetsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_assets', function (Blueprint $table) {
			$table->integer('id')->unsigned();
			$table->string('asset_description_short')->nullable();
			$table->text('asset_description_long')->nullable();
			$table->integer('wa_asset_location_id')->unsigned()->nullable();
			$table->integer('wa_asset_categorie_id')->unsigned()->nullable();
			$table->string('bar_code')->nullable();
			$table->string('serial_number')->nullable();
			$table->integer('wa_asset_depreciation_id')->unsigned()->nullable();
			$table->decimal('depreciation_rate', 20)->nullable();
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
		Schema::drop('wa_assets');
	}
}
