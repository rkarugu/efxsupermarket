<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSuppTransTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_supp_trans', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('dfdnnbfdfdsfdsfsdsdfds');
			$table->string('grn_type_number')->nullable();
			$table->string('supplier_no')->nullable();
			$table->string('suppreference')->nullable();
			$table->date('trans_date')->nullable();
			$table->date('due_date')->nullable();
			$table->boolean('settled')->default(0);
			$table->decimal('rate', 10)->default(0.00);
			$table->decimal('total_amount_inc_vat', 10)->default(0.00);
			$table->string('round_off', 25)->default('0');
			$table->decimal('allocated_amount', 10)->default(0.00);
			$table->decimal('vat_amount', 10)->default(0.00);
			$table->string('document_no')->nullable();
			$table->string('description')->nullable();
			$table->timestamps();
			$table->integer('bill_id')->nullable();
			$table->integer('journel_entry_id')->nullable();
			$table->string('balancing_gl_account')->nullable()->index('balancing_gl_account');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_supp_trans');
	}
}
