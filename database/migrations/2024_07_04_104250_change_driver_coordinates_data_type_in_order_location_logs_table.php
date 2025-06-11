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
            $table->double('driver_lat')->nullable()->change();
            $table->double('driver_lng')->nullable()->change();
            $table->double('driver_distance')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_location_logs', function (Blueprint $table) {
            $table->unsignedInteger('driver_lat')->nullable()->change();
            $table->unsignedInteger('driver_lng')->nullable()->change();
            $table->unsignedInteger('driver_distance')->nullable()->change();
        });
    }
};
