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
        Schema::table('wa_inventory_item_suppliers_prices', function (Blueprint $table) {
            $table->decimal('price', 20, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_suppliers_prices', function (Blueprint $table) {
            $table->decimal('price', 20, 2)->nullable(false)->change();
        });
    }
};
