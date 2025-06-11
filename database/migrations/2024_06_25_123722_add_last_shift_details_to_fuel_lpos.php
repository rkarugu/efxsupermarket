<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fuel_lpos', function (Blueprint $table) {
            $table->double('last_shift_fuel')->nullable();
            $table->double('last_shift_mileage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_lpos', function (Blueprint $table) {
            $table->dropColumn(['last_shift_fuel', 'last_shift_mileage']);
        });
    }
};
