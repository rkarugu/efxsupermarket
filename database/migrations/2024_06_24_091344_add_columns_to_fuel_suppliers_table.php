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
        Schema::table('fuel_suppliers', function (Blueprint $table) {
            $table->integer('wa_suppliers_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_suppliers', function (Blueprint $table) {
            $table->dropColumn('wa_suppliers_id');
        });
    }
};
