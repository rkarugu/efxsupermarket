<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaJournalEntriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_journal_entries', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('dedfdsfdsgdfggdfgdfgdfgdfg');
			$table->string('journal_entry_no')->nullable();
			$table->string('slug')->nullable();
			$table->date('date_to_process')->nullable();
			$table->string('entry_type')->nullable();
			$table->enum('status', array('pending', 'processed'))->default('pending');
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
		Schema::drop('wa_journal_entries');
	}
}
