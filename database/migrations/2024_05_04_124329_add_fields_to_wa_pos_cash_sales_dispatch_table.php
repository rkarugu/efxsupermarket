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
        Schema::table('wa_pos_cash_sales_dispatch', function (Blueprint $table) {
            $table->unsignedInteger('wa_unit_of_measure_id')->nullable();
            $table->string('status')->default('dispatching');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_dispatch', function (Blueprint $table) {
            $table->dropColumn(['wa_unit_of_measure_id', 'dispatching']);
        });
    }
};
