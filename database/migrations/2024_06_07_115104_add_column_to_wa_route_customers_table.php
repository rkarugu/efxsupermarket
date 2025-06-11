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
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->unsignedInteger('rejected_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_route_customers', function (Blueprint $table) {
            $table->dropColumn('rejected_by');
        });
    }
};
