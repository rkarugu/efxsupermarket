<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBankDepositCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bank_deposit_categories', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_bank_deposite_id')->nullable();
			$table->string('receiver_type')->nullable();
			$table->integer('received_from_id')->unsigned()->nullable()->comment('wa_customers table or wa_suppliers table');
			$table->integer('account_id')->unsigned()->nullable()->comment('Wa Chart of accounts');
			$table->string('description')->nullable();
			$table->integer('payment_method_id')->unsigned()->nullable()->comment('payment_methods');
			$table->string('ref_no')->nullable();
			$table->decimal('amount', 20)->nullable()->default(0.00);
			$table->integer('vat_id')->unsigned()->nullable()->comment('tax_managers');
			$table->decimal('tax_percent', 20)->nullable()->default(0.00);
			$table->decimal('total', 20)->nullable()->default(0.00);
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
		Schema::drop('wa_bank_deposit_categories');
	}
}
