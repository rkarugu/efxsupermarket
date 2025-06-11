<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBillPaymentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bill_payments', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('wa_bill_id')->unsigned()->nullable();
			$table->integer('supplier_id')->unsigned()->nullable()->comment('Supplier id');
			$table->integer('bank_account_id')->unsigned()->nullable()->comment('Chart of account id');
			$table->text('mailing_address')->nullable();
			$table->text('memo')->nullable();
			$table->decimal('amount', 20)->nullable();
			$table->date('payment_date')->nullable();
			$table->string('ref_no')->nullable();
			$table->decimal('opening_balance', 20)->nullable();
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
		Schema::drop('wa_bill_payments');
	}
}
