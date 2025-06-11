<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostedSalesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posted_sales', function (Blueprint $table) {
			$table->increments('id');
			$table->date('sales_date')->nullable();
			$table->decimal('gross_sale', 15)->default(0.00);
			$table->decimal('vat', 15)->default(0.00);
			$table->decimal('catering_levy', 15)->default(0.00);
			$table->decimal('service_tax', 15)->default(0.00);
			$table->decimal('net_sales', 15)->default(0.00);
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
		Schema::drop('posted_sales');
	}
}
