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
        Schema::create('delivery_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('vehicle_id')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->timestamp('loading_time')->nullable();
            $table->timestamp('expected_delivery_date');
            $table->timestamp('actual_delivery_date')->nullable();
            $table->string('status')->comment('consolidating, consolidated, loaded, in_progress,finished');
            $table->foreign('route_id')->on('routes')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_schedules');
    }
};
