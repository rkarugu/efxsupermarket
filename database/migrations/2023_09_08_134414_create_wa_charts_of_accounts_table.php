<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaChartsOfAccountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_charts_of_accounts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('account_name')->nullable();
			$table->string('slug')->nullable();
			$table->string('account_code')->nullable();
			$table->integer('wa_account_group_id')->unsigned()->nullable()->index('delete_it_when_acgroup_is_deleted');
			$table->enum('pl_or_bs', array('PROFIT AND LOSS', 'BALANCE SHEET'));
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
		Schema::drop('wa_charts_of_accounts');
	}
}
