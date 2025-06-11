<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFormsItemsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inspection_forms_items', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('inspection_form_id')->nullable();
			$table->integer('inspection_from_type_id')->nullable();
			$table->string('title')->nullable();
			$table->string('short_description')->nullable();
			$table->string('instructions')->nullable();
			$table->string('pass_label')->nullable();
			$table->string('fail_label')->nullable();
			$table->enum('enable_for_submission', array('1', '0'))->nullable();
			$table->enum('require_photo_or_comment_for_pass', array('1', '0'))->nullable();
			$table->enum('require_photo_or_comment_for_fail', array('1', '0'))->nullable();
			$table->enum('require_secondary_meter', array('1', '0'))->nullable();
			$table->enum('require_photo_verification', array('1', '0'))->nullable();
			$table->string('choices')->nullable();
			$table->integer('passing_range_from')->nullable();
			$table->integer('passing_range_to')->nullable();
			$table->dateTime('date')->nullable();
			$table->dateTime('date_time')->nullable();
			$table->enum('status', array('1', '0'))->nullable()->default('1');
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
		Schema::drop('inspection_forms_items');
	}
}
