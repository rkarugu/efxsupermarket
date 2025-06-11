<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAssetMaintenanceTaskTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_asset_maintenance_task', function (Blueprint $table) {
			$table->bigInteger('id');
			$table->integer('wa_asset_category_id')->unsigned()->nullable();
			$table->text('task_description')->nullable();
			$table->bigInteger('days_before_due')->nullable();
			$table->integer('responsible_id')->unsigned()->nullable();
			$table->integer('manager_id')->unsigned()->nullable();
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
		Schema::drop('wa_asset_maintenance_task');
	}
}
