<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaInventoryCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_inventory_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->string('category_code')->nullable();
			$table->string('category_description')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_stock_type_category_id')->unsigned()->nullable()->index('dasddadhjghjdshghjgfbnbvn');
			$table->integer('wa_stock_family_group_id')->unsigned()->nullable()->index('dasddadhjghjdshghj');
			$table->integer('stock_gl_code_id')->unsigned()->nullable()->index('dsfdsfnbncvbfdsfdsfdfsdf');
			$table->integer('wip_gl_code_id')->unsigned()->nullable()->index('dsfdsfdscvcjghjghj');
			$table->integer('stock_adjustments_gl_code_id')->unsigned()->nullable()->index('dsfdsfnbncvbfdsfdsf');
			$table->integer('internal_stock_issues_gl_code_id')->unsigned()->nullable()->index('dsfdsfnbn');
			$table->integer('price_variance_gl_code_id')->unsigned()->nullable()->index('dsfdsfnbncvbfds');
			$table->integer('usage_variance_gl_code_id')->unsigned()->nullable()->index('dasddadhjghj');
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
		Schema::drop('wa_inventory_categories');
	}
}
