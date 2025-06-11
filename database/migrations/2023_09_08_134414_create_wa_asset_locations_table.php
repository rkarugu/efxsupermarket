<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAssetLocationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_asset_locations', function (Blueprint $table) {
			$table->integer('id')->unsigned();
			$table->string('location_ID')->nullable();
			$table->string('location_description')->nullable();
			$table->integer('wa_asset_locations_id')->unsigned()->nullable();
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
		Schema::drop('wa_asset_locations');
	}
}
