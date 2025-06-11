<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'telematics';

    /**
     * Run the migrations.
     */
    public function up(): void
    {/**
        Schema::connection('telematics')->table('vehicle_telematics', function (Blueprint $table) {
            $table->renameColumn('vehicle_number', 'device_number');
        });**/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('telematics')->table('vehicle_telematics', function (Blueprint $table) {
            //
        });
    }
};
