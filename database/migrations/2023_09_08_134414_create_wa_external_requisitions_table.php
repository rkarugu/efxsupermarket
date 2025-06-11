<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaExternalRequisitionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_external_requisitions', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('dsfsdfxcvvxv');
			$table->string('purchase_no')->nullable();
			$table->string('slug')->nullable();
			$table->date('requisition_date')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable()->index('dsfsdfxcvvxvdffds');
			$table->integer('wa_department_id')->unsigned()->nullable()->index('dsfsdfxcvvxvdffdssdfds');
			$table->enum('status', array('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'RESOLVED'))->default('UNAPPROVED');
			$table->enum('is_hide', array('Yes', 'No'))->default('No');
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
		Schema::drop('wa_external_requisitions');
	}
}
