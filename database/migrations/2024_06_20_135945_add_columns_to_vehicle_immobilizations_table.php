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
        Schema::table('vehicle_immobilizations', function (Blueprint $table) {
            $table->integer('time')->comment('Delay in Seconds');
            $table->integer('speed')->comment('Switch Off speed in Kmph');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_immobilizations', function (Blueprint $table) {
            $table->dropColumn(['time', 'speed']);
        });
    }
};
