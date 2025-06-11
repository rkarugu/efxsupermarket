<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStepNumberToInventoryItemProcesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_process_wa_inventory_item', function (Blueprint $table) {
            $table->unsignedInteger('step_number')->nullable()->comment('Unique for process-item combination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_process_wa_inventory_item', function (Blueprint $table) {
            $table->dropColumn(['step_number']);
        });
    }
}
