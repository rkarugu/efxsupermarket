<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tax_managers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title')->nullable();
			$table->string('slug')->nullable();
			$table->float('tax_value', 10)->default(0.00);
			$table->enum('tax_format', array('FIXED', 'PERCENTAGE'))->default('PERCENTAGE');
			$table->integer('input_tax_gl_account')->unsigned()->nullable()->index('fdgfdgdsdgdfg');
			$table->integer('output_tax_gl_account')->unsigned()->nullable()->index('fdgfdgdsdgdfgdsf');
			$table->enum('status', array('0', '1'))->default('1');
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
		Schema::drop('tax_managers');
	}
}
