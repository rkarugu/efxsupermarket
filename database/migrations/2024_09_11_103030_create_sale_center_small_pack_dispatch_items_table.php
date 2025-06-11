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
        Schema::create('sale_center_small_pack_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dispatch_id');
            $table->unsignedInteger('bin_id');
            $table->unsignedInteger('wa_inventory_item_id');
            $table->double('total_quantity');
            $table->double('dispatched_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_center_small_pack_dispatch_sheet_items');
    }
};
