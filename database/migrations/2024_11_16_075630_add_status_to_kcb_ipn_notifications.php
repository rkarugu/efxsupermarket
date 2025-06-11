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
        Schema::table('kcb_ipn_notifications', function (Blueprint $table) {
            $table->string('status')->default('hanging');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kcb_ipn_notifications', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};
