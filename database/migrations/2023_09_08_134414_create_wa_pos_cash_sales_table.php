<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaPosCashSalesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_pos_cash_sales', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('sales_no', 250)->nullable();
			$table->date('date')->nullable();
			$table->time('time')->nullable();
			$table->integer('user_id')->nullable()->index('user_id_of_logged_in_user');
			$table->string('customer', 250)->nullable();
			$table->string('customer_pin')->nullable();
			$table->bigInteger('customer_phone_number')->nullable();
			$table->integer('payment_method_id')->nullable()->index('payment_method_id_chart_of_accounts');
			$table->decimal('cash', 20)->nullable();
			$table->decimal('change', 20)->nullable();
			$table->text('upload_data')->nullable();
			$table->string('status', 200)->nullable()->default('PENDING');
			$table->integer('print_count')->nullable()->default(0);
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
		Schema::drop('wa_pos_cash_sales');
	}
}
