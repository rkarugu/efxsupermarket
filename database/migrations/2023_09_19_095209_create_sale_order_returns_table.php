<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_order_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('wa_internal_requisition_item_id')->nullable();
            $table->foreign('wa_internal_requisition_item_id')
            ->references('id')->on('wa_internal_requisition_items');

            $table->unsignedInteger('item_return_reason_id')->nullable();
            $table->foreign('item_return_reason_id')
            ->references('id')->on('item_return_reasons');

            $table->text('comment')->nullable();
            $table->integer('image');
            $table->integer('quantity');

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
        Schema::dropIfExists('sale_order_returns');
    }
}
