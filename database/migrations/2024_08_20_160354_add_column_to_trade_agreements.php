<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_agreements', function (Blueprint $table) {
            $table->string('quarterly_cycle_start')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trade_agreements', function (Blueprint $table) {
            $table->dropColumn('quarterly_cycle_start');
        });
    }
};
