<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPettyCashTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_petty_cash', function (Blueprint $table) {
			$table->timestamps();
			$table->increments('id');
			$table->decimal('total_amount', 20)->nullable()->default(0.00);
			$table->string('petty_cash_no', 250)->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_petty_cash');
	}
}
