<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaRouteCustomersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_route_customers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('created_by')->unsigned()->nullable()->index('created_by');
			$table->integer('route_id')->unsigned()->nullable()->index('route_id');
			$table->integer('customer_id')->unsigned()->nullable()->index('customer_id');
			$table->string('name', 200)->nullable();
			$table->string('phone', 200)->nullable();
			$table->string('bussiness_name', 200)->nullable();
			$table->string('town', 200)->nullable();
			$table->string('contact_person', 200)->nullable();
			$table->timestamps();
			$table->bigInteger('center_id')->unsigned()->nullable()->index('wa_route_customers_center_id_foreign');
			$table->bigInteger('delivery_centres_id')->unsigned()->nullable()->index('wa_route_customers_delivery_centres_id_foreign');
			$table->float('lat', 10, 0)->default(0);
			$table->float('lng', 10, 0)->default(0);

			// $table->string('location_name')->nullable(); Added in new migration
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_route_customers');
	}
}
