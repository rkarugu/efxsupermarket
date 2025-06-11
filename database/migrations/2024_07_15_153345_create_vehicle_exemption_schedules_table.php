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
        Schema::create('vehicle_exemption_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_type')->comment('5pm-shutdown, 10pm-shutdown,  4am-shutdown');
            $table->string('vehicle_ids');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_exemption_schedules');
    }
};
