<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaAccountGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_account_groups', function (Blueprint $table) {
			$table->increments('id');
			$table->string('group_name')->nullable();
			$table->string('slug')->nullable();
			$table->integer('wa_account_section_id')->unsigned()->nullable()->index('delete_it_when_ac_section_is_deleted');
			$table->enum('is_parent', array('0', '1'))->default('1');
			$table->integer('parent_id')->unsigned()->nullable()->index('delete_it_when_parent_is_deleted');
			$table->enum('profit_and_loss', array('Y', 'N'))->nullable();
			$table->string('sequence_in_tb')->nullable();
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
		Schema::drop('wa_account_groups');
	}
}
