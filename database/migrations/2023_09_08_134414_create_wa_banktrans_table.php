<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBanktransTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_banktrans', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('cashier_id')->unsigned()->nullable()->index('gfdgdfgfdgdfgdfgfdgdfgfdghgj');
			$table->string('type_number')->nullable();
			$table->string('document_no')->nullable();
			$table->string('bank_gl_account_code')->nullable();
			$table->string('reference')->nullable();
			$table->enum('amountCleared', array('0'))->default('0');
			$table->enum('exrate', array('0', '1'))->default('1');
			$table->enum('functionalExrate', array('0', '1'))->default('1');
			$table->dateTime('trans_date')->nullable();
			$table->integer('wa_payment_method_id')->unsigned()->nullable()->index('fgfdgfgfdgfdgfdgfdgfgdffggdfg');
			$table->decimal('amount', 10)->default(0.00);
			$table->integer('wa_curreny_id')->unsigned()->nullable()->index('vvcbbnmyytytretertre');
			$table->timestamps();
			$table->string('balancing_gl_account')->nullable()->unique('balancing_gl_account');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_banktrans');
	}
}
