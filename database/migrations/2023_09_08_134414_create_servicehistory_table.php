<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicehistoryTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servicehistory', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('vehicle')->nullable();
			$table->string('odometer')->nullable();
			$table->date('start_date')->nullable();
			$table->date('completion_date')->nullable();
			$table->string('vendor')->nullable();
			$table->string('reference')->nullable();
			$table->string('photos')->nullable();
			$table->string('documents')->nullable();
			$table->string('comments')->nullable();
			$table->string('issues')->nullable();
			$table->string('general_notes')->nullable();
			$table->string('parts')->nullable();
			$table->string('labor')->nullable();
			$table->string('subtotal')->nullable();
			$table->string('discount')->nullable();
			$table->string('tax')->nullable();
			$table->float('total', 10, 0)->nullable();
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
		Schema::drop('servicehistory');
	}
}
