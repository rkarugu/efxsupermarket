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
        Schema::table('wa_inventory_items', function (Blueprint $table) {
            $table->double('last_grn_cost')->nullable();
            $table->double('weighted_average_cost')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_items', function (Blueprint $table) {
            $table->dropColumn('last_grn_cost');
            $table->dropColumn('weighted_average_cost');
        });
    }
};
