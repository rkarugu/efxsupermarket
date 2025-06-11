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
        Schema::table('wa_stock_breaking', function (Blueprint $table) {
            $table->unsignedInteger('pos_stock_break_request_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_stock_breaking', function (Blueprint $table) {
            $table->dropColumn('pos_stock_break_request_id');
        });
    }
};
