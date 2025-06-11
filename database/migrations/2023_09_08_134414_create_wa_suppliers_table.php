<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSuppliersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_suppliers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('supplier_code')->nullable();
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->string('address')->nullable();
			$table->string('country')->nullable();
			$table->string('telephone')->nullable();
			$table->string('facsimile')->nullable();
			$table->string('email')->nullable();
			$table->text('url')->nullable();
			$table->string('supplier_type')->nullable();
			$table->date('supplier_since')->nullable();
			$table->string('bank_reference')->nullable();
			$table->integer('wa_payment_term_id')->unsigned()->nullable()->index('gfdfgdfkhjkyrtyrtyrty');
			$table->integer('wa_currency_manager_id')->unsigned()->nullable()->index('gfdfgdfkhjkyrtyrtyrtyfbvbv');
			$table->string('remittance_advice')->nullable();
			$table->string('tax_group')->nullable();
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
		Schema::drop('wa_suppliers');
	}
}
