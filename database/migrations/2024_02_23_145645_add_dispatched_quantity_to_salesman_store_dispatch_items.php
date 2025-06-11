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
        Schema::table('salesman_shift_store_dispatch_items', function (Blueprint $table) {
            $table->double('dispatched_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salesman_store_dispatch_items', function (Blueprint $table) {
            $table->dropColumn('dispatched_quantity');
        });
    }
};
