<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpContactsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_contacts', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('postal_addess')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('country')->nullable();
			$table->string('mobile')->nullable();
			$table->string('email_address', 245)->nullable();
			$table->string('emergency_contact_cellphone')->nullable();
			$table->string('street_address')->nullable();
			$table->string('town')->nullable();
			$table->string('home_telephone')->nullable();
			$table->string('work_telephone')->nullable();
			$table->string('emergency_contact_person')->nullable();
			$table->string('emergency_contact_relationship')->nullable();
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
		Schema::drop('wa_emp_contacts');
	}
}
