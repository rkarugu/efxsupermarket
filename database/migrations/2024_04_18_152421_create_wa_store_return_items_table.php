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
        Schema::create('wa_store_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wa_store_return_id');
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->integer('quantity');
            $table->double('weight');
            $table->double('cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_store_return_items');
    }
};
