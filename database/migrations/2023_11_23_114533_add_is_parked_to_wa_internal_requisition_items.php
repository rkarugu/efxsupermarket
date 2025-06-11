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
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->boolean('is_parked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_internal_requisition_items', function (Blueprint $table) {
            $table->dropColumn(['is_parked']);
        });
    }
};
