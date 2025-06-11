<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaCompanyPreferencesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_company_preferences', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('slug')->nullable();
			$table->string('official_company_number')->nullable();
			$table->string('tax_authority_reference')->nullable();
			$table->text('address')->nullable();
			$table->string('latitute')->nullable();
			$table->string('longitude')->nullable();
			$table->string('telephone_number')->nullable();
			$table->string('facsimile_number')->nullable();
			$table->string('email_address')->nullable();
			$table->string('home_currency')->nullable();
			$table->integer('debtors_control_gl_account')->unsigned()->nullable()->index('sasasasa');
			$table->integer('creditors_control_gl_account')->unsigned()->nullable()->index('sasasasad');
			$table->integer('payroll_net_pay_clearing_gl_account')->unsigned()->nullable()->index('sasasasads');
			$table->integer('goods_received_clearing_gl_account')->unsigned()->nullable()->index('sasasasadsd');
			$table->integer('retained_earning_clearing_gl_account')->unsigned()->nullable()->index('sasasasadsdss');
			$table->integer('freight_recharged_gl_account')->unsigned()->nullable()->index('sasasasadsdsssd');
			$table->integer('sales_exchange_variances_gl_account')->unsigned()->nullable()->index('sasasasadsdsssf');
			$table->integer('purchases_exchange_variances_gl_account')->unsigned()->nullable()->index('sasasasadsdsssfd');
			$table->integer('payment_discount_gl_account')->unsigned()->nullable()->index('sasasasadsdsssfdqdx');
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
		Schema::drop('wa_company_preferences');
	}
}
