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
        Schema::table('user_denied_accesses', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->string('request_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_denied_accesses', function (Blueprint $table) {
            $table->dropColumn('is_approved');
            $table->dropColumn('request_response');
        });
    }
};
