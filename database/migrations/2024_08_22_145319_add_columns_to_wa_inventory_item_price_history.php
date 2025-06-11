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
        Schema::table('wa_inventory_item_price_history', function (Blueprint $table) {
            $table->renameColumn('old_weighted-cost', 'old_weighted_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_price_history', function (Blueprint $table) {
            $table->renameColumn('old_weighted_cost', 'old_weighted-cost');

        });
    }
};
