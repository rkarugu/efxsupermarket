<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpRefereesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_referees', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('name')->nullable();
			$table->string('postal_code')->nullable();
			$table->string('email')->nullable();
			$table->string('postal_address')->nullable();
			$table->string('notes')->nullable();
			$table->string('organization')->nullable();
			$table->string('profession')->nullable();
			$table->string('cellphone')->nullable();
			$table->string('physical_address')->nullable();
			$table->string('memo')->nullable();
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
		Schema::drop('wa_emp_referees');
	}
}
