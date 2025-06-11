<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNWaInternalRequisitionItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('n_wa_internal_requisition_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wa_internal_requisition_id')->unsigned()->nullable()->index('fgdfgdfgfddsfdsfdsfdsf');
            $table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('fgdfgdfgfddsfdsfdsfdsfdfdsfd');
            $table->decimal('quantity', 10)->default(0.00);
            $table->decimal('issued_quanity', 10)->default(0.00);
            $table->decimal('standard_cost', 10)->default(0.00);
            $table->decimal('total_cost', 10)->default(0.00);
            $table->decimal('vat_rate', 10)->default(0.00);
            $table->decimal('vat_amount', 10)->default(0.00);
            $table->decimal('total_cost_with_vat', 10)->default(0.00);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->integer('store_location_id')->unsigned()->nullable();
            $table->decimal('selling_price', 20)->nullable()->default(0.00);
            $table->boolean('is_dispatched')->nullable()->default(0);
            $table->integer('dispatched_by')->unsigned()->nullable();
            $table->dateTime('dispatched_time')->nullable();
            $table->string('dispatch_no', 250)->nullable();
            $table->decimal('prev_standard_cost', 20)->nullable();
            $table->decimal('order_price', 20)->nullable();
            $table->integer('supplier_uom_id')->nullable();
            $table->integer('pack_size_id')->nullable();
            $table->integer('unit_conversion')->nullable();
            $table->string('item_no', 250)->nullable();
            $table->string('is_exclusive_vat', 250)->nullable();
            $table->integer('unit_of_measure')->nullable();
            $table->decimal('discount_amount', 20)->nullable();
            $table->decimal('discount_percentage', 20)->nullable();
            $table->integer('tax_manager_id')->nullable();
            $table->decimal('round_off', 20)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('n_wa_internal_requisition_items');
    }
}
