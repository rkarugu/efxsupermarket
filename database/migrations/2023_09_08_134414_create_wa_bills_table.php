<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaBillsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_bills', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('supplier_id')->nullable();
			$table->integer('terms_id')->nullable();
			$table->text('mailing_address')->nullable();
			$table->date('bill_date')->nullable();
			$table->date('due_date')->nullable();
			$table->string('bill_no')->nullable();
			$table->text('memo')->nullable();
			$table->string('attachment')->nullable();
			$table->integer('restaurant_id')->nullable();
			$table->string('tax_amount_type', 200)->nullable();
			$table->boolean('is_processed')->nullable()->default(0);
			$table->timestamps();
			$table->decimal('subTotal', 20)->nullable();
			$table->decimal('total', 20)->nullable();
			$table->decimal('balance', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_bills');
	}
}
