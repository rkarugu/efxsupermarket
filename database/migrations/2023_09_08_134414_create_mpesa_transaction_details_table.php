<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaTransactionDetailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mpesa_transaction_details', function (Blueprint $table) {
			$table->increments('id');
			$table->string('mpesa_request_id')->nullable();
			$table->text('details')->nullable();
			$table->enum('is_done', array('0', '1'))->nullable()->default('0');
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
		Schema::drop('mpesa_transaction_details');
	}
}
