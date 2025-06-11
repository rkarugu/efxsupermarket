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
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->double('distance_estimate')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropColumn(['distance_estimate']);
        });
    }
};
