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
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->unsignedInteger('bin_location_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->dropColumn('bin_location_id');
            $table->dropColumn('branch_id');
        });
    }
};
