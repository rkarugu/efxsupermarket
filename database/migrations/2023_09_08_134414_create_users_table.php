<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->string('slug')->nullable();
			$table->string('password')->nullable();
			$table->string('badge_number')->nullable();
			$table->string('id_number')->nullable();
			$table->string('phone_number', 20)->nullable();
			$table->string('image')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable();
			$table->integer('wa_department_id')->unsigned()->nullable()->index('fbhmmgdfdsfdsfdsfdsretretretre');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id');
			$table->integer('role_id')->unsigned()->index('delete_user_when_its_role_delete');
			$table->integer('category_id')->nullable();
			$table->enum('gender', array('Male', 'Female'))->nullable();
			$table->string('nationality')->nullable();
			$table->date('dob')->nullable();
			$table->date('date_employeed')->nullable();
			$table->string('complementary_number')->nullable();
			$table->float('complementary_amount', 10)->nullable()->default(0.00);
			$table->string('max_discount_percent', 5)->nullable()->default('0');
			$table->enum('authorization_level', array('1', '2', '3', '4', '5'))->nullable();
			$table->enum('external_authorization_level', array('1', '2', '3', '4', '5'))->nullable();
			$table->enum('purchase_order_authorization_level', array('1', '2', '3', '4', '5'))->nullable();
			$table->enum('status', array('0', '1'))->default('0');
			$table->date('accounting_period_start_date')->nullable();
			$table->date('accounting_period_end_date')->nullable();
			$table->timestamps();
			$table->decimal('credit_limit', 20)->nullable()->default(0.00);
			$table->integer('route')->unsigned()->nullable()->index('route');
			$table->boolean('upload_data')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}
}
