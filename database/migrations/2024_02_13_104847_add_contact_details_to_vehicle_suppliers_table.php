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
        Schema::table('vehicle_suppliers', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('physical_address')->nullable();
            $table->decimal('lat')->nullable();
            $table->decimal('lng')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_suppliers', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'physical_address', 'lat', 'lng']);
        });
    }
};
