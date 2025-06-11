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
        Schema::table('wa_inventory_item_supplier_data', function (Blueprint $table) {
            $table->decimal('price_list_cost', 10, 2)->nullable();
            $table->date('price_list_cost_effective_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_supplier_data', function (Blueprint $table) {
            $table->dropColumn('price_list_cost');
            $table->dropColumn('price_list_cost_effective_from');
        });
    }
};
