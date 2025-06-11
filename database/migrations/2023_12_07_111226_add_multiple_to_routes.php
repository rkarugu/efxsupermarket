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
        Schema::table('routes', function (Blueprint $table) {
            $table->double('manual_distance_estimate')->nullable()->default(0);
            $table->double('manual_fuel_estimate')->nullable()->default(0);
            $table->double('manual_rate_estimate')->nullable()->default(0);
            $table->double('ctn_target')->nullable()->default(0);
            $table->double('dzn_target')->nullable()->default(0);
            $table->double('travel_expense')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['manual_distance_estimate', 'manual_fuel_estimate', 'manual_rate_estimate', 'ctn_target', 'dzn_target', 'travel_expense']);
        });
    }
};
