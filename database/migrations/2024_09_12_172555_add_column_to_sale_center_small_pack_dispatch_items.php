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
        Schema::table('sale_center_small_pack_dispatch_items', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->nullable();
        });

        Schema::table('sale_center_small_pack_dispatch_statuses', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->nullable();
        });

        Schema::table('sale_center_small_pack_items', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->nullable();
        });

        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_center_small_pack_dispatch_items', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });

        Schema::table('sale_center_small_pack_dispatch_statuses', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });

        Schema::table('sale_center_small_pack_items', function (Blueprint $table) {
            $table->dropColumn('center_id');
        });

        Schema::table('sale_center_small_pack_dispatches', function (Blueprint $table) {
            $table->unsignedInteger('center_id')->nullable();
        });
    }
};
