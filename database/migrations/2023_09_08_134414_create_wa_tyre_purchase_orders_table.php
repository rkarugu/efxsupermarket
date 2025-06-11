<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaTyrePurchaseOrdersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_tyre_purchase_orders', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('gfdbnhjgddsfdgmhmhjghjj');
			$table->string('purchase_no')->nullable();
			$table->string('slug')->nullable();
			$table->integer('restaurant_id')->unsigned()->nullable()->index('gfdbnhjgddsfdgmhmhjghjjssd');
			$table->integer('wa_department_id')->unsigned()->nullable()->index('gfdbnhjgddsfdgmhmhjghjjssdcvc');
			$table->integer('wa_supplier_id')->unsigned()->nullable()->index('gfdbnhjgddsfdgmhmhjghjjsdfsdcvcdf');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('gfdbnhjgddsfdgmhmhjghjjsdfsdcvc');
			$table->date('purchase_date')->nullable();
			$table->enum('status', array('UNAPPROVED', 'PENDING', 'PROCESSING', 'APPROVED', 'DECLINED', 'PRELPO', 'COMPLETED'))->default('UNAPPROVED');
			$table->enum('return_status', array('Returned', 'Not Returned'))->nullable()->default('Not Returned');
			$table->enum('is_hide', array('Yes', 'No'))->default('No');
			$table->enum('is_printed', array('0', '1'))->default('0');
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
		Schema::drop('wa_tyre_purchase_orders');
	}
}
