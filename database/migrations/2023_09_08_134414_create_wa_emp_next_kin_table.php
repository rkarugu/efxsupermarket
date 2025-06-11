<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpNextKinTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_next_kin', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('job_title_id')->nullable();
			$table->string('name')->nullable();
			$table->string('relationship')->nullable();
			$table->string('email')->nullable();
			$table->string('postal_address')->nullable();
			$table->string('organization')->nullable();
			$table->string('memo')->nullable();
			$table->string('profession')->nullable();
			$table->string('cellphone');
			$table->string('physical_address');
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
		Schema::drop('wa_emp_next_kin');
	}
}
