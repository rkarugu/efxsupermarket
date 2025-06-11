<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RouteOrderTakingDaySalesmanCoordinatesFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->boolean('check_sales_man_coordinates')->default(false);
            $table->boolean('check_is_order_taking_day')->default(false);
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
            $table->dropColumn(['check_sales_man_coordinates', 'check_is_order_taking_day']);
        });
    }
}