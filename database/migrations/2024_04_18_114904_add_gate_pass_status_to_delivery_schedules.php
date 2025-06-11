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
            $table->string('gate_pass_status')->default('pending')->comment('pending', 'initiated', 'verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_schedules', function (Blueprint $table) {
            $table->dropColumn(['gate_pass_status']);
        });
    }
};
