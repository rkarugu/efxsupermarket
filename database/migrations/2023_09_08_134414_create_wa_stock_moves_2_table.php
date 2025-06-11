<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockMoves2Table extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_moves_2', function (Blueprint $table) {
			$table->bigInteger('id', true)->unsigned();
			$table->integer('user_id')->unsigned()->nullable()->index('gfdgfgdfgdfhgfhgfhgfhgfhgfh')->comment('logged user id');
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('gfdgdfgdfttrthhgfgfdfd');
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('wa_internal_requisition_id_2');
			$table->integer('stock_adjustment_id')->unsigned()->nullable();
			$table->integer('wa_inventory_location_transfer_id')->unsigned()->nullable()->index('dsfdsfdsfdsfdsfdfdsfdsfdsfdsf');
			$table->integer('ordered_item_id')->unsigned()->nullable()->index('ordered_item_id');
			$table->integer('restaurant_id')->unsigned()->nullable()->index('gfdgfgdfgdfhgfhgfhgfhghdsfs');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('gfdgfgdfgdfhgfhgfhgfhghdsfgfdgdfgs');
			$table->integer('wa_pos_cash_sales_id')->unsigned()->nullable()->index('wa_pos_cash_sales_id_2');
			$table->string('stock_id_code')->nullable()->index('stock_id_code')->comment('item stock code id');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('gfdgdfgdfgdfgghgfhgfh');
			$table->string('grn_type_number')->nullable()->comment('type number from number series of grn');
			$table->integer('grn_last_nuber_used')->nullable()->comment('trans_number');
			$table->decimal('price', 10)->default(0.00)->comment('price = price-discount');
			$table->string('period_number')->nullable()->comment('period number from number series');
			$table->string('refrence')->nullable()->comment('suppliernumber+name+purchase_order_number');
			$table->string('qauntity')->default('0.00')->comment('system qty');
			$table->decimal('new_qoh', 20)->nullable()->default(0.00);
			$table->integer('shift_id')->nullable();
			$table->decimal('discount_percent', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->string('document_no')->nullable();
			$table->timestamps();
			$table->decimal('selling_price', 20)->nullable();
			$table->string('item_type')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_stock_moves_2');
	}
}
