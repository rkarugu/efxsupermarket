<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemSalesWithGlCodesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_sales_with_gl_codes', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('food_item_id')->unsigned()->nullable()->index('fdsvcxvbvcbvc');
			$table->string('item_title')->nullable();
			$table->integer('family_group_id')->unsigned()->nullable()->index('fdsvcxvbvcbvcffdfdsfvc');
			$table->integer('gl_code_id')->unsigned()->nullable()->index('fdsvcxvbvcbvcfdsfmmnb');
			$table->decimal('quantity', 10)->nullable();
			$table->decimal('gross_sale', 10)->nullable();
			$table->decimal('vat', 10)->nullable();
			$table->integer('restaurant_id')->nullable();
			$table->decimal('catering_levy', 10)->nullable();
			$table->decimal('service_tax', 10)->nullable();
			$table->decimal('net_sales', 10)->nullable();
			$table->date('sale_date')->nullable();
			$table->enum('is_posted', array('0', '1'))->default('0');
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
		Schema::drop('item_sales_with_gl_codes');
	}
}
