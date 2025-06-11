<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewFuelEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_fuel_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fuel_lpo_id');
            $table->double('distance_estimate');
            $table->double('distance_covered');
            $table->double('fuel_estimate');
            $table->double('fuel_consumed');
            $table->double('consumption_rate');
            $table->double('fuel_price');
            $table->string('fuel_type')->nullable();
            $table->double('pre_mileage');
            $table->double('current_mileage');
            $table->string('image')->nullable();
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
        Schema::dropIfExists('new_fuel_entries');
    }
}
