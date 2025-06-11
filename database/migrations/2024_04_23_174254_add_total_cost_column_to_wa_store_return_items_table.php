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
        Schema::table('wa_store_return_items', function (Blueprint $table) {
            $table->double('total_cost')->after('cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_store_return_items', function (Blueprint $table) {
            $table->dropColumn('total_cost');
        });
    }
};
