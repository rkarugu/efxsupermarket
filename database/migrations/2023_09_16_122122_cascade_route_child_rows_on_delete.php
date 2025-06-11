<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CascadeRouteChildRowsOnDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_centres', function (Blueprint $table) {
            $table->dropForeign('delivery_centres_route_id_foreign');
            $table->foreign('route_id')->references('id')->on('routes')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropForeign('wa_route_customers_delivery_centres_id_foreign');
            $table->dropForeign('wa_route_customers_center_id_foreign');
            $table->foreign('delivery_centres_id')->references('id')->on('delivery_centres')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
