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
        Schema::table('pack_sizes', function (Blueprint $table) {
            $table->string('pack_size')->nullable();
            $table->integer('ctn')->nullable();
            $table->integer('dzn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pack_sizes', function (Blueprint $table) {
            $table->dropColumn('pack_size');
            $table->dropColumn('ctn');
            $table->dropColumn('dzn');
        });
    }
};
