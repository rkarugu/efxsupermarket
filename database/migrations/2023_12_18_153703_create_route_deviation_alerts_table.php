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
        Schema::create('route_deviation_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('driver_id');
            $table->string('status')->comment('options: switched_off,switched_on');
            $table->timestamp('switch_on_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_deviation_alerts');
    }
};
