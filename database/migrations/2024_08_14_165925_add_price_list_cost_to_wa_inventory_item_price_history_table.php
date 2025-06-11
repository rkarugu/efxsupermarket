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
            $table->decimal('price_list_cost', 15, 2)->nullable()->after('selling_price');
            $table->decimal('old_price_list_cost', 15, 2)->nullable()->after('old_selling_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_price_history', function (Blueprint $table) {
            $table->dropColumn('price_list_cost');
            $table->dropColumn('old_price_list_cost');
        });
    }
};
