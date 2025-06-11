<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaGlTransTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_gl_trans', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('fdsfdsfdsffdgdfgdfgdfgdf');
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('wa_internal_requisition_id');
			$table->integer('stock_adjustment_id')->unsigned()->nullable()->index('gdfgdfgdfgdfgfdgdfgdf');
			$table->integer('wa_supp_tran_id')->unsigned()->nullable()->index('fdsfdsfdsdffdsfdsfdsfdsdf');
			$table->integer('wa_debtor_tran_id')->unsigned()->nullable()->index('sadsadsadsadsadsadsadsad');
			$table->integer('wa_sales_invoice_id')->unsigned()->nullable()->index('fdsfdsfdsfdsfdshfghjfjh');
			$table->integer('wa_credit_note_id')->unsigned()->nullable();
			$table->integer('wa_journal_entrie_id')->unsigned()->nullable()->index('fdsfdsfdsfdsfdsfdsfdsfdsfsdfsdf');
			$table->integer('wa_pos_cash_sales_id')->unsigned()->nullable()->index('wa_pos_cash_sales_id');
			$table->integer('restaurant_id')->nullable()->index('restaurant_id');
			$table->integer('shift_id')->nullable()->index('shift_id');
			$table->integer('salesman_id')->nullable()->index('salesman_id');
			$table->string('grn_type_number')->nullable()->index('grn_type_number');
			$table->string('transaction_type')->nullable();
			$table->string('transaction_no')->nullable()->index('transaction_no');
			$table->integer('grn_last_used_number')->nullable()->comment('trans number');
			$table->dateTime('trans_date')->nullable();
			$table->string('period_number')->nullable();
			$table->string('supplier_account_number')->nullable();
			$table->string('account')->nullable()->index('account');
			$table->decimal('amount', 15)->default(0.00);
			$table->text('narrative')->nullable();
			$table->string('cheque_image')->nullable();
			$table->string('reference')->nullable();
			$table->integer('banking_expense_id')->nullable()->index('banking_expense_id');
			$table->string('banking_expense_type', 200)->nullable();
			$table->timestamps();
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('balancing_gl_account')->nullable()->index('balancing_gl_account');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_gl_trans');
	}
}
