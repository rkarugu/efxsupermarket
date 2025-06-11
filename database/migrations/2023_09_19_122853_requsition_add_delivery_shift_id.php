<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequsitionAddDeliveryShiftId extends Migration
{
    
    public function up()
    {
        //
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->unsignedInteger('wa_delivery_shift_id')->nullable();
            $table->foreign('wa_delivery_shift_id')
            ->references('id')->on('wa_shifts');
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
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->dropColumn('wa_delivery_shift_id');
        });
    }
}
