<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaJournalEntrieItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_journal_entrie_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('wa_journal_entry_id')->unsigned()->nullable()->index('gfdgdfgdfgdfgdfdfg');
			$table->integer('gl_account_id')->unsigned()->nullable()->index('gfdgdfgdfgdfgdfdfgs');
			$table->decimal('credit', 10)->default(0.00);
			$table->decimal('debit', 10)->default(0.00);
			$table->string('entry_type')->nullable();
			$table->string('narrative')->nullable();
			$table->string('reference')->nullable();
			$table->integer('restaurant_id')->nullable();
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
		Schema::drop('wa_journal_entrie_items');
	}
}
