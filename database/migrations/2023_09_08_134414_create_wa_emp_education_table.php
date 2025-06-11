<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpEducationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_education', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->string('course')->nullable();
			$table->string('institution')->nullable();
			$table->date('to')->nullable();
			$table->string('point')->nullable();
			$table->string('memo')->nullable();
			$table->integer('education_level_id')->nullable();
			$table->date('from')->nullable();
			$table->integer('job_grade_id')->nullable();
			$table->string('ranking')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_emp_education');
	}

}
