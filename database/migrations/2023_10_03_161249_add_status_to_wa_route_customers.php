<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToWaRouteCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->string('status')->default('unverified')->comment('unverified, verified, approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
