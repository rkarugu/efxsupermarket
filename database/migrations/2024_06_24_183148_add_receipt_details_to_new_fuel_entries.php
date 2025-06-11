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
        Schema::table('new_fuel_entries', function (Blueprint $table) {
            $table->string('receipt_no');
            $table->string('receipt_image');
            $table->string('dashboard_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_fuel_entries', function (Blueprint $table) {
            $table->dropColumn(['receipt_no', 'receipt_image', 'dashboard_image']);
        });
    }
};
