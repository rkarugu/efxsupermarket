<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpDependentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_dependents', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('name')->nullable();
			$table->string('memo')->nullable();
			$table->string('relationship')->nullable();
			$table->date('date_of_birth')->nullable();
			$table->string('cellphone')->nullable();
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
		Schema::drop('wa_emp_dependents');
	}
}
