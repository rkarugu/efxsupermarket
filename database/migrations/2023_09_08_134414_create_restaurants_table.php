<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('restaurants', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug');
			$table->string('image');
			$table->string('floor_image')->nullable();
			$table->string('opening_time', 50)->nullable();
			$table->string('closing_time', 50)->nullable();
			$table->string('location')->nullable();
			$table->string('latitude')->nullable();
			$table->string('longitude')->nullable();
			$table->string('branch_code')->nullable();
			$table->integer('wa_company_preference_id')->unsigned()->nullable()->index('fgfgfgnbnlmjfjhjfgjhg');
			$table->string('telephone')->nullable();
			$table->string('mpesa_till')->nullable();
			$table->string('website_url')->nullable();
			$table->string('email')->nullable();
			$table->string('pin')->nullable();
			$table->string('vat')->nullable();
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
		Schema::drop('restaurants');
	}
}
