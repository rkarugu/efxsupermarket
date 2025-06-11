<?php

use App\Enums\FuelEntryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('new_fuel_entries');

        Schema::create('fuel_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('fuel_station_id');
            $table->unsignedInteger('fueled_by');
            $table->string('lpo_number')->unique();
            $table->string('entry_status')->default(FuelEntryStatus::Pending->value);
            $table->string('shift_type')->nullable();
            $table->unsignedInteger('shift_id')->nullable();
            $table->double('last_fuel_entry_level')->nullable();
            $table->double('last_fuel_entry_mileage')->nullable();
            $table->timestamp('start_shift_time')->nullable();
            $table->double('start_shift_fuel_level')->nullable();
            $table->double('start_shift_mileage')->nullable();
            $table->timestamp('end_shift_time')->nullable();
            $table->double('end_shift_fuel_level')->nullable();
            $table->double('end_shift_mileage')->nullable();
            $table->double('end_shift_odometer')->nullable();
            $table->double('manual_distance_covered')->nullable();
            $table->double('manual_consumption_rate')->nullable();
            $table->double('required_fuel_quantity')->nullable();
            $table->timestamp('fueling_time')->nullable();
            $table->double('post_fueling_level')->nullable();
            $table->double('actual_fuel_quantity')->nullable();
            $table->double('shift_distance_estimate')->nullable();
            $table->double('shift_fuel_estimate')->nullable();
            $table->double('shift_consumption_rate_estimate')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_entries');
    }
};
