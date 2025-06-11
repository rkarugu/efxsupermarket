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
        Schema::table('n_wa_inventory_location_transfers', function (Blueprint $table) {
            $table->enum('status', array('DRAFT', 'PENDING', 'COMPLETED'))
                ->default('PENDING')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('n_wa_inventory_location_transfers', function (Blueprint $table) {
            $table->enum('status', array('PENDING', 'COMPLETED'))
                ->default('PENDING')->change();
        });
    }
};
