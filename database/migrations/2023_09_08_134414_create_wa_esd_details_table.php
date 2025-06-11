<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEsdDetailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_esd_details', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('invoice_number')->nullable();
			$table->string('cu_serial_number')->nullable();
			$table->string('cu_invoice_number')->nullable();
			$table->text('verify_url')->nullable();
			$table->string('description')->nullable();
			$table->boolean('status')->default(0);
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
		Schema::drop('wa_esd_details');
	}
}
