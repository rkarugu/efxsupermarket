<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintClassUsersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('print_class_users', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable()->index('delete_user_when_there_restoress_is_deleted');
			$table->integer('print_class_id')->unsigned()->nullable()->index('delete_user_when_there_print_class_is_deleted');
			$table->string('username')->nullable();
			$table->string('password')->nullable();
			$table->enum('can_cancle_item', array('0', '1'))->default('0');
			$table->enum('can_print_bill', array('1', '0'))->default('0');
			$table->enum('status', array('0', '1'))->default('1');
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
		Schema::drop('print_class_users');
	}
}
