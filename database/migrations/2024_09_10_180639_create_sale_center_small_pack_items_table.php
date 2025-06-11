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
        Schema::create('sale_center_small_pack_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sale_center_small_pack_id');
            $table->unsignedInteger('wa_inventory_item_id');
            $table->unsignedInteger('wa_internal_requisition_item_id');
            $table->unsignedInteger('wa_route_customer_id');
            $table->string('requisition_no');
            $table->unsignedInteger('bin_id');
            $table->string('quantity',125);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_center_small_pack_items');
    }
};
