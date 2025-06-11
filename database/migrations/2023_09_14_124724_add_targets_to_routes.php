<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTargetsToRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->double('tonnage_target')->nullable()->default(0);
            $table->double('sales_target')->nullable()->default(0);
            $table->enum('order_taking_day', [1, 2, 3, 4, 5, 6, 7])->nullable()->comment('Each value corresponds to the day of the week');
            $table->enum('delivery_day', [1, 2, 3, 4, 5, 6, 7])->nullable()->comment('Each value corresponds to the day of the week');
            $table->string('starting_location_name')->nullable();
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
            $table->dropColumn(['tonnage_target', 'sales_target', 'order_taking_day', 'delivery_day', 'starting_location_name']);
        });
    }
}
