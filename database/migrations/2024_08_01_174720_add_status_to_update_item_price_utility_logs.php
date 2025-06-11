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
        Schema::table('update_item_price_utility_logs', function (Blueprint $table) {
            $table->integer('b_r')->nullable()->after('wa_inventory_item_price_id');
            $table->integer('s_p')->nullable()->after('b_r');
            $table->integer('s_c')->nullable()->after('s_p');
            $table->string('status')->nullable()->after('s_c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_item_price_utility_logs', function (Blueprint $table) {
            $table->dropColumn('b_r');
            $table->dropColumn('s_p');
            $table->dropColumn('s_c');
            $table->dropColumn('status');
        });
    }
};
