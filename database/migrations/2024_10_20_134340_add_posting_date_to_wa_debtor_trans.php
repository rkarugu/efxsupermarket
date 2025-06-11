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
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->timestamp('unverified_resolved_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_debtor_trans', function (Blueprint $table) {
            $table->dropColumn(['unverified_resolved_date']);
        });
    }
};
