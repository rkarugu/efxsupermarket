<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnableMultipleOrderTakingAndDeliveryDaysOnRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['order_taking_day', 'delivery_day']);
            $table->string('order_taking_days')->nullable()->comment('Values correspond to days of the week');
            $table->string('delivery_days')->nullable()->comment('Values correspond to days of the week');
            $table->boolean('allows_same_day_delivery')->default(false);
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
            $table->dropColumn(['allows_same_day_delivery', 'order_taking_days', 'delivery_days']);
        });
    }
}
