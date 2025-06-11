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
        Schema::table('route_polylines', function (Blueprint $table) {
            $table->longText('lat_lngs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_polylines', function (Blueprint $table) {
            $table->dropColumn(['lat_lngs']);
        });
    }
};
