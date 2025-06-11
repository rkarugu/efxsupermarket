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
        Schema::create('display_bin_user_item_allocations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('wa_location_and_store_id');
            $table->integer('bin_id');
            $table->integer('wa_inventory_item_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_bin_user_item_allocations');
    }
};
