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
        Schema::table('vehicle_telematics_data', function (Blueprint $table) {
            $table->string('timestamp')->nullable();
            $table->string('raw_timestamp')->nullable();
            $table->double('fuel_level')->nullable();
            $table->double('mileage')->nullable();
            $table->double('speed')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('ignition_status')->nullable();
            $table->double('direction')->nullable();
            $table->integer('data_index')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_telematics_data', function (Blueprint $table) {
            $table->dropColumn(['timestamp', 'fuel_level', 'mileage', 'speed', 'latitude', 'longitude', 'ignition_status', 'direction']);
        });
    }
};
