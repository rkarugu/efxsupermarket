<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoryRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_category_relations', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('item_id')->unsigned()->index('delete_relation_when_item_is_deleted');
			$table->integer('category_id')->unsigned()->index('delete_relation_when_category_is_deleted');
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
		Schema::drop('item_category_relations');
	}
}
