<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEntitlementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_entitlements', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('employee_id')->nullable();
			$table->string('entitlement')->nullable();
			$table->string('opening_balance')->nullable();
			$table->string('leave_period');
			$table->integer('leave_type_id')->nullable();
			$table->string('default_entitlement');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_entitlements');
	}

}
