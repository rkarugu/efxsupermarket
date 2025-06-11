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
        Schema::create('vehicle_custom_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('action')->comment('1 => switch-off, 0 => switch-on');
            $table->string('time');
            $table->string('vehicle_ids')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_custom_schedules');
    }
};
