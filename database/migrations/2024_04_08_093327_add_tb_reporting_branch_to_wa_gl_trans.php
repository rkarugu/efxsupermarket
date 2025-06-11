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
        Schema::table('wa_gl_trans', function (Blueprint $table) {
            $table->unsignedInteger('tb_reporting_branch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_gl_trans', function (Blueprint $table) {
            $table->dropColumn(['tb_reporting_branch']);
        });
    }
};
