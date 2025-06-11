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
//        Schema::table('trade_agreements', function (Blueprint $table) {
//            $table->boolean('linked_to_portal')->default(true)->nullable();
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_agreements', function (Blueprint $table) {
            $table->dropColumn('linked_to_portal');
        });
    }
};
