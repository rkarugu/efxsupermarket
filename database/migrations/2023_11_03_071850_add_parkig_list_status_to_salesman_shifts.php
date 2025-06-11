<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParkigListStatusToSalesmanShifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wa_shifts', function (Blueprint $table) {
            $table->string('parking_list_status')->nullable()->default('open');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_vehicles', function (Blueprint $table) {
            $table->dropColumn(['parking_list_status']);
        });
    }
}
