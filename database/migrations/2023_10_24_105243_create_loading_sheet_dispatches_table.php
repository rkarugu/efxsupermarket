<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadingSheetDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_sheet_dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('delivery_note_number');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('shift_id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('user_id')->comment('Dispatcher/Store Keeper');
            $table->string('delivery_status')->default('not_started')->comment('not_started, in_progress, finished');
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
        Schema::dropIfExists('loading_sheet_dispatches');
    }
}
