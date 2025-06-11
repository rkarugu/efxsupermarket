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
        Schema::create('small_pack_driver_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('small_pack_driver_dispatch_id');
            $table->unsignedInteger('sale_center_small_pack_dispatch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('small_pack_driver_dispatch_items');
    }
};
