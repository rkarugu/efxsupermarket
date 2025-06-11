<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaDepartmentsAuthorizationRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_departments_authorization_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('deleteitwhenempisdeleted');
			$table->integer('wa_department_id')->unsigned()->nullable()->index('deleteitwhendepartmentisdeleted');
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
		Schema::drop('wa_departments_authorization_relations');
	}
}
