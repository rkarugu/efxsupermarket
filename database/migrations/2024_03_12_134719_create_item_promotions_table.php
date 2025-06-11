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
        Schema::create('item_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('inventory_item_id'); 
            $table->foreign('inventory_item_id')->references('id')->on('wa_inventory_items')->onDelete('cascade');
            $table->integer('sale_quantity');
            $table->unsignedInteger('promotion_item_id'); 
            $table->foreign('promotion_item_id')->references('id')->on('wa_inventory_items')->onDelete('cascade');
            $table->integer('promotion_quantity');
            $table->unsignedInteger('initiated_by'); 
            $table->foreign('initiated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_promotions');
    }
};
