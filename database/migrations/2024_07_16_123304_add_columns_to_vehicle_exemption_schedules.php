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
        Schema::table('vehicle_exemption_schedules', function (Blueprint $table) {
            $table->string('status')->default('open')->comment('closed to show it cannot be edited since command has run');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_exemption_schedules', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};
