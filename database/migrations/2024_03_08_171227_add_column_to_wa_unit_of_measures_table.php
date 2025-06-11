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
        Schema::table('wa_unit_of_measures', function (Blueprint $table) {
            $table->tinyInteger('is_display')->default(0)->comment('0 or 1');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_unit_of_measures', function (Blueprint $table) {
            $table->dropColumn('is_display');

        });
    }
};
