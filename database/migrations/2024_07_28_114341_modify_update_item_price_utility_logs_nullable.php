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
        Schema::table('update_item_price_utility_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('wa_location_and_store_id')->nullable()->change();
            $table->unsignedBigInteger('wa_inventory_item_price_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_item_price_utility_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('wa_location_and_store_id')->nullable(false)->change();
            $table->unsignedBigInteger('wa_inventory_item_price_id')->nullable(false)->change();
        });
    }
};
