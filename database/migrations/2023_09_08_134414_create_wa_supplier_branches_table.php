<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaSupplierBranchesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_supplier_branches', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('wa_supplier_id')->nullable()->index('wa_supplier_id');
			$table->integer('restaurant_id')->nullable()->index('restaurant_id');
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
		Schema::drop('wa_supplier_branches');
	}
}
