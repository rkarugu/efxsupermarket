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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->string('license_plate_number')->unique();
            $table->string('vin')->unique();
            $table->unsignedBigInteger('vehicle_type_id');
            $table->foreignId('vehicle_model_id')->nullable()->constrained('vehicle_models')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->double('unladen_weight')->nullable();
            $table->double('max_load_capacity')->nullable();
            $table->double('axle_count')->nullable();
            $table->double('tyre_count')->nullable();
            $table->double('onboarding_mileage')->nullable();
            $table->timestamp('onboarding_mileage_date')->nullable();
            $table->double('onboarding_fuel')->nullable();
            $table->timestamp('onboarding_fuel_date')->nullable();
            $table->string('device_name')->nullable();
            $table->string('sim_card_number')->nullable();
            $table->double('fuel_tank_capacity')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->timestamp('acquisition_date')->nullable();
            $table->double('acquisition_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
