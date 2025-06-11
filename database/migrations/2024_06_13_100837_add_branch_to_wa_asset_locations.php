<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wa_asset_locations', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->unsignedBigInteger('restaurant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_asset_locations', function (Blueprint $table) {            
            $table->dropColumn('restaurant_id');
            $table->unsignedInteger('id')->change();
            $table->dropPrimary('id');
        });
    }
};
