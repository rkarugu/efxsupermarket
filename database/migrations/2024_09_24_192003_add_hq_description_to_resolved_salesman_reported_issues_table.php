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
        Schema::table('resolved_salesman_reported_issues', function (Blueprint $table) {
            $table->longText('hq_description')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resolved_salesman_reported_issues', function (Blueprint $table) {
            $table->dropColumn('hq_description');
        });
    }
};
