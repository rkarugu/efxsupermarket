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
        Schema::create('update_item_price_utility_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiated_by');
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->unsignedBigInteger('wa_location_and_store_id');
            $table->unsignedBigInteger('wa_inventory_item_price_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_item_price_utility_logs');
    }
};
