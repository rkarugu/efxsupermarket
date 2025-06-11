<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_schedule_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('wa_inventory_item_id');
            $table->double('total_quantity');
            $table->double('received_quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_schedule_items');
    }
};
