<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpIndisciplineCategoryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_Indiscipline_category', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->integer('indiscipline_category_id')->nullable();
			$table->date('effective_date')->nullable();
			$table->integer('action_id')->nullable();
			$table->string('cost_charge')->nullable();
			$table->string('indiscipline')->nullable();
			$table->string('loction')->nullable();
			$table->text('descrption')->nullable();
			$table->text('attach_letter')->nullable();
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
		Schema::drop('wa_emp_Indiscipline_category');
	}
}
