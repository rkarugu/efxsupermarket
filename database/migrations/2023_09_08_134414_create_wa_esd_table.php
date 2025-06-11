<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEsdTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_esd', function (Blueprint $table) {
			$table->integer('id', true);
			$table->text('signature')->nullable();
			$table->boolean('is_used')->nullable()->default(0);
			$table->integer('last_used_by')->unsigned()->nullable()->index('last_used_by');
			$table->string('document_no', 250)->nullable();
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
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
		Schema::drop('wa_esd');
	}
}
