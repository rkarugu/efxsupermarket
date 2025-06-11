<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bills', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('deleted_bill_when_user_is_deleted');
			$table->string('slug')->nullable();
			$table->string('bill_narration')->nullable();
			$table->integer('print_count')->default(0);
			$table->enum('status', array('PENDING', 'COMPLETED'))->default('PENDING');
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
		Schema::drop('bills');
	}
}
