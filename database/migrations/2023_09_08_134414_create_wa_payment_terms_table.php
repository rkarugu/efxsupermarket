<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPaymentTermsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_payment_terms', function (Blueprint $table) {
			$table->increments('id');
			$table->string('term_code')->nullable();
			$table->string('slug')->nullable();
			$table->string('term_description')->nullable();
			$table->enum('due_after_given_month', array('0', '1'));
			$table->string('days_in_following_months')->nullable();
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
		Schema::drop('wa_payment_terms');
	}
}
