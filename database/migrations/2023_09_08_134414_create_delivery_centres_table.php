<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryCentresTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('delivery_centres', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->string('name');
			$table->string('lat');
			$table->string('lng');
			$table->timestamps();
			$table->integer('route_id')->unsigned()->nullable()->index('delivery_centres_route_id_foreign');

			// Added in another migration, add_center_location_name_to_delivery_centres>
			// $table->string('center_location_name')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('delivery_centres');
	}
}
