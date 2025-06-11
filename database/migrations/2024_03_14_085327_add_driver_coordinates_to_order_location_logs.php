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
        Schema::table('order_location_logs', function (Blueprint $table) {
            $table->unsignedInteger('driver_id')->nullable();
            $table->unsignedInteger('driver_lat')->nullable();
            $table->unsignedInteger('driver_lng')->nullable();
            $table->unsignedInteger('driver_distance')->nullable();
            $table->unsignedInteger('delivery_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_location_logs', function (Blueprint $table) {
            //
        });
    }
};
