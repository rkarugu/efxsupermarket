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
        Schema::create('update_bin_inventory_utility_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->unsignedBigInteger('wa_location_and_store_id');
            $table->unsignedBigInteger('wa_unit_of_measure_id');
            $table->unsignedBigInteger('pending_approval_status')->default(0);
            $table->unsignedBigInteger('rejected_bin_status')->default(0);
            $table->unsignedBigInteger('existing_bin_status')->default(0);
            $table->unsignedBigInteger('approved')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_bin_inventory_utility_logs');
    }
};
