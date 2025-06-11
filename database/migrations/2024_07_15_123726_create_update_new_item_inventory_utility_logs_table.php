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
        Schema::create('update_new_item_inventory_utility_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->unsignedBigInteger('wa_inventory_item_approval_status_id');
            $table->string('wa_inventory_item_supplier_id');
            $table->unsignedBigInteger('wa_inventory_location_uom_id');
            $table->boolean('has_duplicate')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_new_item_inventory_utility_logs');
    }
};
