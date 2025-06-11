<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSupremeStoreRequisitionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_supreme_store_requisitions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('requisition_no')->nullable();
			$table->string('slug');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id_users');
			$table->integer('restaurant_id')->unsigned()->index('restaurant_id_resturant_branch');
			$table->integer('wa_department_id')->unsigned()->index('wa_department_id_department');
			$table->integer('to_store_id')->unsigned()->nullable()->index('to_store_id');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id_location')->comment('from location');
			$table->date('requisition_date')->nullable();
			$table->enum('status', array('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'COMPLETED'))->default('UNAPPROVED');
			$table->string('manual_doc_no', 250)->nullable();
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
		Schema::drop('wa_supreme_store_requisitions');
	}
}
