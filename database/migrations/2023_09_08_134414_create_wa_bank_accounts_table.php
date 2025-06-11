<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBankAccountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bank_accounts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->nullable();
			$table->integer('bank_account_gl_code_id')->unsigned()->nullable()->index('deleteitwhenaccountisdeletedfdfdf');
			$table->string('account_name')->nullable();
			$table->string('account_code')->nullable();
			$table->string('account_number')->nullable();
			$table->text('bank_address')->nullable();
			$table->string('currency')->nullable();
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
		Schema::drop('wa_bank_accounts');
	}
}
