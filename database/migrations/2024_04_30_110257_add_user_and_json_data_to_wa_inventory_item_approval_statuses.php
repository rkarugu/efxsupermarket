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
        Schema::table('wa_inventory_item_approval_statuses', function (Blueprint $table) {
            $table->timestamp('approval_date')->nullable();
            $table->json('new_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_item_approval_statuses', function (Blueprint $table) {
            $table->dropColumn('approval_date');
            $table->dropColumn('new_data');
        });
    }
};
