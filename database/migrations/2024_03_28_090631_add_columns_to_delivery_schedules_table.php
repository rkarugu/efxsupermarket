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
        Schema::table('delivery_schedules', function (Blueprint $table) {
            $table->boolean('has_gatepass')->default(false)->nullable();
            $table->boolean('gatepass_verified')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_schedules', function (Blueprint $table) {
            $table->dropColumn('has_gatepass');
            $table->dropColumn('gatepass_verified');
        });
    }
};
