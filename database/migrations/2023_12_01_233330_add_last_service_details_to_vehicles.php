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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->double('odometer_adjustment')->nullable();
            $table->unsignedBigInteger('last_service_interval_id')->nullable();
            $table->double('last_service_mileage')->nullable();
            $table->timestamp('last_service_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['last_service_interval_id', 'last_service_mileage', 'last_service_date']);
        });
    }
};
