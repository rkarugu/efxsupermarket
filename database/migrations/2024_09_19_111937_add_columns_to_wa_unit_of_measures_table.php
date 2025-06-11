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
            $table->integer('chief_storekeeper')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_unit_of_measures', function (Blueprint $table) {
            $table->dropColumn('chief_storekeeper');
        });
    }
};
