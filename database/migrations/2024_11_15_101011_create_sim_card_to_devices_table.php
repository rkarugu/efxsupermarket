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
        Schema::create('sim_card_to_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('simcard_id');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedBigInteger('simcard_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sim_card_to_devices');

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('simcard_id');
        });
    }
};
