<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToWaStoreCRequisitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('wa_store_c_requisitions', function(Blueprint $table)
		{
			$table->foreign('to_store_id', 'delete_requisitions_when_delete_location')->references('id')->on('wa_location_and_stores')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('restaurant_id', 'restaurant_id_resturant_branch')->references('id')->on('restaurants')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'user_id_users')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('wa_department_id', 'wa_department_id_department')->references('id')->on('wa_departments')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('wa_location_and_store_id', 'wa_location_and_store_id_location')->references('id')->on('wa_location_and_stores')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('wa_store_c_requisitions', function(Blueprint $table)
		{
			$table->dropForeign('delete_requisitions_when_delete_location');
			$table->dropForeign('restaurant_id_resturant_branch');
			$table->dropForeign('user_id_users');
			$table->dropForeign('wa_department_id_department');
			$table->dropForeign('wa_location_and_store_id_location');
		});
	}

}
