<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaRecipeIngredientsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_recipe_ingredients', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_recipe_id')->unsigned()->nullable()->index('wa_recipe_id');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id');
			$table->string('weight')->nullable();
			$table->string('material_cost')->nullable();
			$table->string('weight_portion')->nullable();
			$table->decimal('number_of_portion', 10);
			$table->string('cost')->nullable();
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
		Schema::drop('wa_recipe_ingredients');
	}
}
