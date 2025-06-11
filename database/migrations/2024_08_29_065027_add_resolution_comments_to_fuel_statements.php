<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fuel_statements', function (Blueprint $table) {
            $table->boolean('unknown_resolved')->default(false);
            $table->boolean('unknown_approved')->default(false);
            $table->text('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_statements', function (Blueprint $table) {
            $table->dropColumn(['comments', 'unknown_resolved', 'unknown_approved']);
        });
    }
};
