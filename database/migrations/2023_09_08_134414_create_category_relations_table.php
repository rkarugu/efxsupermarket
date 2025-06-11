<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('parent_id')->unsigned()->nullable()->index('parent_id');
			$table->integer('category_id')->unsigned()->nullable()->index('category_id');
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
		Schema::drop('category_relations');
	}
}
