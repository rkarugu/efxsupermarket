<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadingSheetDispatchItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_sheet_dispatch_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('loading_sheet_dispatch_id');
            $table->foreign('loading_sheet_dispatch_id')->references('id')->on('loading_sheet_dispatches')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('wa_inventory_item_id');
            $table->double('requested_quantity');
            $table->double('loaded_quantity');
            $table->unsignedSmallInteger('received_by_deliveryman')->default(0);
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
        Schema::dropIfExists('loading_sheet_dispatch_items');
    }
}
