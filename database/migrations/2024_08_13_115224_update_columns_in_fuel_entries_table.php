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
            $table->string('dashboard_photo')->change();
            $table->string('invoice_photo')->change();
            $table->string('receipt_photo')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->double('dashboard_photo')->change();
            $table->double('invoice_photo')->change();
            $table->double('receipt_photo')->change();
        });
    }
};
