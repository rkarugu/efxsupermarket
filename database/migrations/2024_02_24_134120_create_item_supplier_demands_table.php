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
        Schema::create('item_supplier_demands', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('wa_inventory_item_id');
            $table->unsignedInteger('wa_supplier_id');
            $table->double('current_cost');
            $table->double('new_cost');
            $table->double('current_price');
            $table->double('new_price');
            $table->double('demand_quantity');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_supplier_demands');
    }
};
