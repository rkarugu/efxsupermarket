<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAuditBillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_audit_bills', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('bill_id')->nullable();
			$table->string('trans_type')->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('tyttyuytuhhkuiythgghgh');
			$table->string('order_id')->nullable();
			$table->string('table_no')->nullable()->index('gdfgdfgdfgdfdsabvcbvc');
			$table->string('old_bill_id')->nullable()->index('bvmjbkhjghhdhgfhfgh');
			$table->string('old_table_no')->nullable();
			$table->integer('receipt_id')->unsigned()->nullable()->index('delete_recipe_when_delete_location');
			$table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_audit_bills');
	}

}
