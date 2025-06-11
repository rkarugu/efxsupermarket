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
            $table->double('old_weighted-cost')->nullable();
            $table->double('weighted_cost')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_price_history', function (Blueprint $table) {
            $table->dropColumn('old_weighted-cost');
            $table->dropColumn('weighted_cost');
        });
    }
};
