<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpExperienceTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_experience', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id');
			$table->string('organization');
			$table->integer('job_title_id');
			$table->date('from');
			$table->date('to');
			$table->text('memo');
			$table->text('reason_for_leaving');
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
		Schema::drop('wa_emp_experience');
	}
}
