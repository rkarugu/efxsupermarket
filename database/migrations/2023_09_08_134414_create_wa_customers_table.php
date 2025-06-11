<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCustomersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_customers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('customer_code')->nullable();
			$table->string('customer_name')->nullable();
			$table->string('slug')->nullable();
			$table->text('address')->nullable();
			$table->string('street')->nullable();
			$table->string('town')->nullable();
			$table->string('contact_person')->nullable();
			$table->string('country')->nullable();
			$table->string('telephone')->nullable();
			$table->string('email')->nullable();
			$table->date('customer_since')->nullable();
			$table->decimal('credit_limit', 10)->default(0.00);
			$table->integer('payment_term_id')->unsigned()->nullable()->index('fdfdsfdsfdsfgdfhfghrtretretretretre');
			$table->integer('route_id')->unsigned()->nullable()->index('route_id');
			$table->boolean('is_blocked')->nullable()->default(0);
			$table->timestamps();
			$table->integer('delivery_centres_id');
			$table->bigInteger('user_id')->unsigned()->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_customers');
	}
}
