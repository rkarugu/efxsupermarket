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
        Schema::create('hamper_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('hamper_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('wa_inventory_item_id')->nullable();
            $table->unsignedInteger('demand_id')->nullable();
            $table->decimal('standard_cost')->nullable();
            $table->decimal('selling_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hamper_items');
    }
};
