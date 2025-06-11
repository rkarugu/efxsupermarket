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
        Schema::table('wa_demands', function (Blueprint $table) {
            $table->boolean('merged')->default(0);
            $table->json('merged_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_demands', function (Blueprint $table) {
            $table->dropColumn('merged');
            $table->dropColumn('merged_from');
        });
    }
};
