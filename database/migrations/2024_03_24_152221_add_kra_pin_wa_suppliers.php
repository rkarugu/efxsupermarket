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
        Schema::table('wa_suppliers', function (Blueprint $table) {
            $table->string('kra_pin')->nullable()->after('tax_withhold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_suppliers', function (Blueprint $table) {
            $table->dropColumn('kra_pin');
        });
    }
};
