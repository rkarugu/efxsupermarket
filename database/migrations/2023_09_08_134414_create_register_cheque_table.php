<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterChequeTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('register_cheque', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('cheque_no', 250)->nullable();
			$table->string('drawers_name', 250)->nullable();
			$table->string('drawers_bank', 250)->nullable();
			$table->decimal('amount', 20)->nullable();
			$table->date('cheque_date')->nullable();
			$table->integer('salesman_id')->unsigned()->nullable()->index('salesman_id');
			$table->string('batch_no', 250)->nullable();
			$table->date('date_received')->nullable();
			$table->text('cheque_image')->nullable();
			$table->timestamps();
			$table->enum('status', array('Registered', 'Deposited', 'Cleared', 'Bounced'))->nullable()->default('Registered');
			$table->date('deposited_date')->nullable();
			$table->integer('deposited_by')->unsigned()->nullable()->index('deposited_by');
			$table->string('bank_deposited', 250)->nullable();
			$table->date('clearance_date')->nullable();
			$table->boolean('is_bounced_transfer')->nullable()->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('register_cheque');
	}
}
