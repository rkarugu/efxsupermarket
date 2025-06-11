<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaDebtorTrans25072023Table extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_debtor_trans_25072023', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_sales_invoice_id')->unsigned()->nullable()->index('dfdfgdgfghfdgdfgdfgdfgfdfdg');
			$table->integer('wa_credit_note_id')->unsigned()->nullable();
			$table->integer('salesman_id')->nullable()->comment('id comes from Wa_location_and_store table');
			$table->integer('salesman_user_id')->unsigned()->nullable()->index('salesman_user_id')->comment('id comes from users table where salesmen_id = wa_store_location.id');
			$table->string('type_number')->nullable();
			$table->integer('wa_customer_id')->unsigned()->nullable()->index('dfdfgdgfghfdgdfgdfgdfgfdfderewrewg');
			$table->string('customer_number')->nullable();
			$table->date('trans_date')->nullable();
			$table->dateTime('input_date')->nullable();
			$table->integer('wa_accounting_period_id')->unsigned()->nullable()->index('dfdfgdgfghfdgdfgdgdfgffgdfgfdfderewrewg');
			$table->string('reference')->nullable();
			$table->integer('shift_id')->nullable();
			$table->decimal('amount', 10)->default(0.00);
			$table->decimal('allocated_amount', 10)->default(0.00);
			$table->string('document_no')->nullable();
			$table->enum('is_printed', array('0', '1'))->default('0');
			$table->boolean('is_settled')->default(0);
			$table->timestamps();
			$table->integer('route_id')->unsigned()->nullable();
			$table->string('paid_by', 250)->nullable();
			$table->integer('user_id')->unsigned()->nullable();
			$table->string('invoice_customer_name', 250)->nullable();
			$table->integer('register_cheque_id')->unsigned()->nullable()->index('register_cheque_id');
			$table->integer('wa_route_customer_id')->nullable()->index('wa_route_customer_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_debtor_trans_25072023');
	}
}
