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
        Schema::create('pos_missing_items', function (Blueprint $table) {
            $table->id();
            $table->integer('reported_by');
            $table->integer('wa_inventory_item_id');
            $table->double('as_at_qoh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_missing_items');
    }
};
