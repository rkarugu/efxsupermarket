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
        Schema::create('stock_break_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('stock_break_dispatches')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('child_item_id');
            $table->double('child_quantity');
            $table->string('child_pack_size');
            $table->unsignedInteger('mother_item_id');
            $table->double('mother_quantity');
            $table->string('mother_pack_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_break_dispatch_items');
    }
};
