<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaStockMovesCTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wa_stock_moves_C', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->nullable()->index('wa_stock_moves_C_user_id_users')->comment('logged user id');
			$table->integer('wa_purchase_order_id')->unsigned()->nullable()->index('wa_purchase_order_id_purchase_order');
			$table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('wa_internal_requisition_id');
			$table->integer('stock_adjustment_id')->unsigned()->nullable();
			$table->integer('wa_inventory_location_transfer_id')->unsigned()->nullable()->index('wa_inventory_location_transfer_id_location');
			$table->integer('ordered_item_id')->unsigned()->nullable()->index('ordered_item_id');
			$table->integer('restaurant_id')->unsigned()->nullable()->index('wa_stock_moves_C_restaurant_id_resturant_branch');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id_location');
			$table->integer('wa_pos_cash_sales_id')->unsigned()->nullable()->index('wa_pos_cash_sales_id');
			$table->string('stock_id_code')->nullable()->index('stock_id_code')->comment('item stock code id');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id_inventory');
			$table->string('grn_type_number')->nullable()->comment('type number from number series of grn');
			$table->integer('grn_last_nuber_used')->nullable()->comment('trans_number');
			$table->decimal('price', 10)->default(0.00)->comment('price = price-discount');
			$table->string('period_number')->nullable()->comment('period number from number series');
			$table->string('refrence')->nullable()->comment('suppliernumber+name+purchase_order_number');
			$table->string('qauntity')->default('0.00')->comment('system qty');
			$table->decimal('new_qoh', 20)->nullable()->default(0.00);
			$table->integer('shift_id')->nullable()->index('shift_id');
			$table->decimal('discount_percent', 10)->default(0.00);
			$table->decimal('standard_cost', 10)->default(0.00);
			$table->string('document_no')->nullable()->index('document_no');
			$table->timestamps();
			$table->integer('wa_store_c_receive_id')->unsigned()->nullable()->index('wa_store_c_receive_id');
			$table->integer('wa_store_c_requisitions_id')->unsigned()->nullable()->index('wa_store_c_requisitions_id');
			$table->integer('wa_supreme_store_requisitions_id')->nullable()->index('wa_supreme_store_requisitions_id');
			$table->integer('wa_supreme_store_receive_id')->nullable()->index('wa_supreme_store_receive_id');
			$table->integer('n_wa_internal_requistion_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('wa_stock_moves_C');
	}
}
