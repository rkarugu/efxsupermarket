<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryManShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deliveryman_id');
            $table->unsignedInteger('salesman_shift_id');
            $table->unsignedInteger('route_id');
            $table->string('status')->default('open');
            $table->string('delivery_note_no');
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
        Schema::dropIfExists('delivery_man_shifts');
    }
}
