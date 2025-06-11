<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderDeliveryStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('wa_internal_requisitions', function (Blueprint $table) {
            $table->boolean('is_delivered')->default(false); // This will create a boolean column named 'is_active' with a default value of true.
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
            $table->dropColumn(['is_delivered']);
        });
    }
}
