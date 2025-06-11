<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('wa_inventory_item_id');
            $table->foreign('wa_inventory_item_id')->references('id')->on('wa_inventory_items')->onDelete('cascade')->onUpdate('cascade');
            $table->double('production_quantity')->default(1);
            $table->unsignedInteger('production_plant_id');
            $table->string('description')->nullable();
            $table->unsignedInteger('current_step_number')->default(1);
            $table->enum('status', ['not_started', 'in_progress', 'paused', 'completed'])->default('not_started');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_work_orders');
    }
}
