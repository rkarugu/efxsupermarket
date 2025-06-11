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
        Schema::table('wa_inventory_location_transfer_item_returns', function (Blueprint $table) {
            $table->string('return_reason')->nullable();
            $table->string('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_inventory_location_transfer_item_returns', function (Blueprint $table) {
            $table->dropColumn(['return_reason', 'rejection_reason']);
        });
    }
};
