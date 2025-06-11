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
            $table->boolean('is_processed')->default(1);
        });

        Schema::table('stock_expunged_variations', function (Blueprint $table) {
            $table->unsignedInteger('expunged_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_debtor_tran_items', function (Blueprint $table) {
            $table->dropColumn('is_processed');
        });

        Schema::table('stock_expunged_variations', function (Blueprint $table) {
            $table->dropColumn('expunged_by');
        });
    }
};
