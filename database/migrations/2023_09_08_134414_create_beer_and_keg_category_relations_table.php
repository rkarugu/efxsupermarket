<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeerAndKegCategoryRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('beer_and_keg_category_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('beer_and_keg_category_id')->unsigned()->index('deleted_all_relations_when_category_is_deleted');
			$table->integer('parent_id')->unsigned()->index('deleted_all_relations_whenpcategory_is_deleted');
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
		Schema::drop('beer_and_keg_category_relations');
	}
}
