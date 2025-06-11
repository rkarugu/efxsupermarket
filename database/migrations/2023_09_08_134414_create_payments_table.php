<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('payment_method');
			$table->integer('gl_account_no')->nullable();
			$table->decimal('amount', 10)->default(0.00);
			$table->integer('restaurant_id')->nullable();
			$table->date('date')->nullable();
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
		Schema::drop('payments');
	}
}
