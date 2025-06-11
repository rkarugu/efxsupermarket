<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWaRouteCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('wa_route_customers', function(Blueprint $table)
		{
			$table->foreign('center_id')->references('id')->on('delivery_centres')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('delivery_centres_id')->references('id')->on('delivery_centres')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('wa_route_customers', function(Blueprint $table)
		{
			$table->dropForeign('wa_route_customers_center_id_foreign');
			$table->dropForeign('wa_route_customers_delivery_centres_id_foreign');
		});
	}

}
