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
        Schema::create('wa_return_demand_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wa_return_demand_id');
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->integer('quantity');
            $table->double('cost');
            $table->double('demand_cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_return_demand_items');
    }
};
