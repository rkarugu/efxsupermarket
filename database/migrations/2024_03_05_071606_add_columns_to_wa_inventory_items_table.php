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
            $table->double('vortex_cost')->nullable();
            $table->double('vortex_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_items', function (Blueprint $table) {
            $table->dropColumn('vortex_cost');
            $table->dropColumn('vortex_price');
        });
    }
};
