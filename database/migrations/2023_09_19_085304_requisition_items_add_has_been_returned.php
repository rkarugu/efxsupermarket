<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequisitionItemsAddHasBeenReturned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->boolean('is_returned')->default(false);
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
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->dropColumn('is_returned');
        });
    }
}
