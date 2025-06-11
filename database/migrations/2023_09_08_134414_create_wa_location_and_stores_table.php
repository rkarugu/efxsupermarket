<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaLocationAndStoresTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_location_and_stores', function (Blueprint $table) {
			$table->increments('id');
			$table->string('location_code')->nullable();
			$table->string('location_name')->nullable();
			$table->decimal('credit_limit', 20)->nullable()->default(0.00);
			$table->string('slug')->nullable();
			$table->integer('wa_branch_id')->unsigned()->nullable()->index('fdsfbnbvnyutyuty');
			$table->enum('is_cost_centre', array('0', '1'))->default('0');
			$table->string('account_no', 100)->nullable();
			$table->timestamps();
			$table->integer('route_id')->unsigned()->nullable()->index('route_id');
			$table->string('biller_no', 200)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_location_and_stores');
	}
}
