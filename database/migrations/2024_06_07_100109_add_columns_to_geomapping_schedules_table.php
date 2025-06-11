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
        Schema::table('geomapping_schedules', function (Blueprint $table) {
            $table->unsignedInteger('completed_by')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('supervisor_contact')->nullable();
            $table->unsignedInteger('HQ_approved_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geomapping_schedules', function (Blueprint $table) {
            $table->dropColumn('supervisor_contact');
            $table->dropColumn('supervisor');
            $table->dropColumn('completed_by');
            $table->dropColumn('HQ_approved_by');
        });
    }
};
