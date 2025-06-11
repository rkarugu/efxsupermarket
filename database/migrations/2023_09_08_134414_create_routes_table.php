<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('route_name')->nullable();
            $table->timestamps();
            $table->float('start_lat', 10, 0)->default(0);
            $table->float('start_lng', 10, 0)->default(0);
            $table->float('end_lat', 10, 0)->default(0);
            $table->float('end_lng', 10, 0)->default(0);

            /**
             * Added in new migration (add targets)
             *
             * $table->double('tonnage_target')->default(0);
             * $table->double('sales_target')->default(0);
             * $table->enum('order_taking_day', [1, 2, 3, 4, 5, 6, 7])->nullable()->comment('Each value corresponds to the day of the week');
             * $table->enum('delivery_day', [1, 2, 3, 4, 5, 6, 7])->nullable()->comment('Each value corresponds to the day of the week');
             * $table->string('starting_location_name')->nullable();
             */

            /**
             * Migration enable_order_taking_and_delivery_days
             *
             * $table->string('order_taking_day')->nullable()->comment('Values correspond to days of the week')->change();
             * $table->string('delivery_day')->nullable()->comment('Values correspond to days of the week')->change();
             * $table->renameColumn('order_taking_day', 'order_taking_days');
             * $table->renameColumn('delivery_day', 'delivery_days');
             * $table->boolean('allows_same_day_delivery')->default(false);
             */
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('routes');
    }

}
