<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('route_id');
            $table->foreign('route_id')->references('id')->on('routes')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('start_shop_id')->nullable();
            $table->string('start_lat')->default(0);
            $table->string('start_lng')->default(0);
            $table->unsignedBigInteger('end_shop_id');
            $table->string('end_lat')->default(0);
            $table->string('end_lng')->default(0);
            $table->boolean('start_point_is_plan_start_point')->default(false);
            $table->double('fuel_estimate')->default(0)->comment('In Litres');
            $table->double('distance_estimate')->default(0)->comment('In Kilometers');
            $table->integer('time_estimate')->default(0)->comment('In minutes');
            $table->string('road_condition')->nullable();
            $table->string('road_type')->nullable();
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
        Schema::dropIfExists('route_sections');
    }
}
