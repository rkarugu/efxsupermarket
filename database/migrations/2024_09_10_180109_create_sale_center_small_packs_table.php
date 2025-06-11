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
        Schema::create('sale_center_small_packs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('center_id');
            $table->unsignedInteger('restaurant_id');
            $table->unsignedInteger('route_id');
            $table->boolean('dispatched')->default(false);
            $table->timestamp('dispatch_time')->nullable();
            $table->unsignedInteger('dispatcher_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_center_small_packs');
    }
};
