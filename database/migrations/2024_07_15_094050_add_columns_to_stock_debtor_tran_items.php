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
        Schema::table('stock_debtor_tran_items', function (Blueprint $table) {
            $table->timestamp('stock_date')->nullable();
            $table->unsignedInteger('uom_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_debtor_tran_items', function (Blueprint $table) {
            $table->dropColumn(['stock_date', 'uom_id']);
        });
    }
};
