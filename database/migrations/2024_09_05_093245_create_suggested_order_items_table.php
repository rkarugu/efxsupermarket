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
        Schema::create('suggested_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suggested_order_id');
            $table->foreign('suggested_order_id')->references('id')->on('suggested_orders')->onDelete('cascade');
            $table->unsignedInteger('wa_inventory_item_id')->nullable();
            $table->foreign('wa_inventory_item_id')->references('id')->on('wa_inventory_items')->nullOnDelete();
            $table->decimal('quantity', 20,2);
            $table->decimal('qoo', 20,2);
            $table->decimal('max_stock', 20,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggested_order_items');
    }
};
