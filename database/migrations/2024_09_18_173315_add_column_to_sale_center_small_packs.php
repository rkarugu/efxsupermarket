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
        Schema::table('sale_center_small_packs', function (Blueprint $table) {
            $table->unsignedInteger('shift_id');
        });
        
        Schema::table('small_pack_driver_dispatches', function (Blueprint $table) {
            $table->unsignedInteger('shift_id');
        });

        
        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->unsignedInteger('shift_id');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_center_small_packs', function (Blueprint $table) {
            $table->dropColumn('shift_id');
        });

        Schema::table('small_pack_driver_dispatches', function (Blueprint $table) {
            $table->dropColumn('shift_id');
        });

        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->dropColumn('shift_id');
        });
    }
};
