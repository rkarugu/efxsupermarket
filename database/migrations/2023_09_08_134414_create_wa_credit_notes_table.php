<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCreditNotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_credit_notes', function (Blueprint $table) {
			$table->increments('id');
			$table->string('credit_note_number')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_customer_id')->unsigned()->nullable()->index('wa_customer_id_credit_notes');
			$table->integer('creater_id')->unsigned()->nullable()->index('creater_id_credit_notes');
			$table->date('order_date')->nullable();
			$table->enum('request_or_delivery', array('request', 'delivery'))->default('request');
			$table->enum('status', array('open', 'close'))->default('open');
			$table->enum('order_creating_status', array('pending', 'completed'))->default('pending');
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
		Schema::drop('wa_credit_notes');
	}
}
