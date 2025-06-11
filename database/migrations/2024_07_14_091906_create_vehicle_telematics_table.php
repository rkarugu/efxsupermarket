<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'telematics';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
/**        Schema::connection('telematics')->create('vehicle_telematics', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number');
            $table->longText('data');
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
            $table->timestamps();
        });**/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('telematics')->dropIfExists('vehicle_telematics');
    }
};
