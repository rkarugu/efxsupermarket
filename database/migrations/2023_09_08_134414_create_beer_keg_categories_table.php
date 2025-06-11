<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeerKegCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beer_keg_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('level')->default(0)->comment('0 for major groups 1 for sub major groups 2 for menu items group 3 for famil groups and 4 for sub family groups');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->string('image')->nullable();
			$table->integer('display_order')->default(0);
			$table->string('available_from', 100)->nullable();
			$table->string('available_to', 100)->nullable();
			$table->text('description')->nullable();
			$table->float('price', 10)->default(0.00);
			$table->integer('max_selection_limit')->nullable();
			$table->enum('is_have_another_layout', array('0', '1'))->default('0');
			$table->enum('allow_happy_hours', array('0', '1'))->default('0')->comment('0 for no 1 for yes');
			$table->string('gl_account_no')->nullable();
			$table->boolean('status')->default(1);
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
		Schema::drop('beer_keg_categories');
	}
}
