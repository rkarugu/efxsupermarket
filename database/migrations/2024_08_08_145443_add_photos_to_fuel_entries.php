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
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->double('fuel_price')->nullable();
            $table->double('dashboard_photo')->nullable();
            $table->double('invoice_photo')->nullable();
            $table->double('receipt_photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->dropColumn(['fuel_price', 'dashboard_photo', 'invoice_photo', 'receipt_photo']);
        });
    }
};
