<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wa_demand_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_inventory_item_id');
            $table->unsignedInteger('wa_demand_id');
            $table->double('current_cost');
            $table->double('new_cost');
            $table->double('current_price');
            $table->double('new_price');
            $table->double('demand_quantity');
            // $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_demand_items');
    }
};
