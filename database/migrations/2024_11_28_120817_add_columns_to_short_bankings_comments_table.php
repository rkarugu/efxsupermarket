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
        Schema::table('short_bankings_comments', function (Blueprint $table) {
            $table->string('type')->nullable()->comment('Director', 'Staff short/excess');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_bankings_comments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
