<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCenterRadiusToDeliveryCenters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_centres', function (Blueprint $table) {
            $table->unsignedInteger('preferred_center_radius')->default(1000)->comment('In meters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_centres', function (Blueprint $table) {
            $table->dropColumn(['preferred_center_radius']);
        });
    }
}
