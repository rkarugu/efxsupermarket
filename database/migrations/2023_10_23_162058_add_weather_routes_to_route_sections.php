<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeatherRoutesToRouteSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_sections', function (Blueprint $table) {
            $table->string('rainy_fuel_estimate')->nullable()->after('fuel_estimate');
            $table->string('rainy_distance_estimate')->nullable()->after('distance_estimate');
            $table->string('rainy_time_estimate')->nullable()->after('time_estimate');
            $table->string('rainy_road_condition')->nullable()->after('road_condition');
            $table->string('rainy_road_type')->nullable()->after('road_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('rainy_fuel_estimate');
            $table->dropColumn('rainy_distance_estimate');
            $table->dropColumn('rainy_time_estimate');
            $table->dropColumn('rainy_road_condition');
            $table->dropColumn('rainy_road_type');
        });
    }
}
