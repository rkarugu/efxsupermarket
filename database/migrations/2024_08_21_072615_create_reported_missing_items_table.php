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
        Schema::create('reported_missing_items', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->integer('reported_by');
            $table->double('quantity')->nullable();
            $table->double('as_at_quantity')->nullable();
            $table->integer('wa_location_and_store_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reported_missing_items');
    }
};
