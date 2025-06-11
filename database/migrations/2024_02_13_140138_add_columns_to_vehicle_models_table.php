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
        Schema::table('vehicle_models', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->string('suppliers')->nuullable()->comment('Values correspond to the vehicle supplier ID');
            $table->double('unladed_weight')->nullable();
            $table->double('ma_load_capacity')->nullable();
            $table->double('fuel_tank_capacity')->nullable();
            $table->double('tyre_count')->nullable();
            $table->double('axle_count')->nullable();
            $table->double('travel_expense')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_models', function (Blueprint $table) {
            
        });
    }
};
