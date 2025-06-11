<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaEmpDocumentTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_emp_document', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('emp_id')->nullable();
			$table->text('document')->nullable();
			$table->string('ref_number')->nullable();
			$table->date('issued_by')->nullable();
			$table->date('expiry_date')->nullable();
			$table->text('descrption')->nullable();
			$table->date('issue_date')->nullable();
			$table->date('received_date')->nullable();
			$table->text('select_file')->nullable();
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
		Schema::drop('wa_emp_document');
	}
}
