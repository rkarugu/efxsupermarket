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
        Schema::table('new_fuel_entries', function (Blueprint $table) {
            $table->string('invoice_no')->nullable();
            $table->string('invoice_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_fuel_entries', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'invoice_image']);
        });
    }
};
