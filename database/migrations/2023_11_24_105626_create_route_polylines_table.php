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
        Schema::create('route_polylines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('route_id');
            $table->longText('polyline');
            $table->text('waypoint_order')->nullable();

            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_polylines');
    }
};
