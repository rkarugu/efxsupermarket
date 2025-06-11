<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuelLposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fuel_lpos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lpo_number');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('deliveryman_id');
            $table->double('pre_mileage');
            $table->double('pre_fuel');
            $table->double('distance_estimate');
            $table->double('fuel_estimate');
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
        Schema::dropIfExists('fuel_lpos');
    }
}
