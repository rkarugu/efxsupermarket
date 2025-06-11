<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPermissionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('role_id')->unsigned()->nullable()->index('delete_permissions_when_role_is_deletedd');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('module_name', 100);
			$table->string('module_action', 100);
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
		Schema::drop('user_permissions');
	}
}
