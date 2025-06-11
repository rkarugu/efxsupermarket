<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBankTransfersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bank_transfers', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('transfer_from')->unsigned()->nullable();
			$table->integer('transfer_to')->unsigned()->nullable();
			$table->decimal('amount', 20)->nullable();
			$table->date('date')->nullable();
			$table->text('memo')->nullable();
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
		Schema::drop('wa_bank_transfers');
	}
}
