<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaChequesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_cheques', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('supplier_id')->unsigned()->nullable()->comment('wa_suppliers');
			$table->integer('bank_account_id')->unsigned()->nullable()->comment('Wa Chart of accounts');
			$table->date('payment_date')->nullable();
			$table->string('cheque_no')->nullable();
			$table->text('memo')->nullable();
			$table->text('mailing_address')->nullable();
			$table->string('tax_amount_type')->nullable();
			$table->decimal('total', 20)->nullable()->default(0.00);
			$table->decimal('sub_total', 20)->nullable()->default(0.00);
			$table->integer('restaurant_id')->unsigned()->nullable();
			$table->boolean('is_processed')->nullable()->default(0)->comment('0 = its not processed, 1= processed');
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
		Schema::drop('wa_cheques');
	}
}
