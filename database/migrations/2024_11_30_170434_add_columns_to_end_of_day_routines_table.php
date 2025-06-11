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
        Schema::table('end_of_day_routines', function (Blueprint $table) {
            $table->index('branch_id', 'idx_branch_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('end_of_day_routines', function (Blueprint $table) {
            $table->dropIndex('idx_branch_id');

        });
    }
};
