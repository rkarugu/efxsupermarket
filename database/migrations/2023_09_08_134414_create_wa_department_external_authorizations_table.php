<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaDepartmentExternalAuthorizationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_department_external_authorizations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('fdghgdsfdsfdsdsfdstjr');
			$table->integer('wa_department_id')->unsigned()->index('fdghgdsfdsfdsdsfdstjrfsvb');
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
		Schema::drop('wa_department_external_authorizations');
	}
}
