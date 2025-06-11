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
        Schema::table('wa_pos_cash_sales', function (Blueprint $table) {
            $table->string('customer_phone_number')->nullable()->change();
            $table->integer('dipatched_by')->nullable();
            $table->string('dispatched_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales', function (Blueprint $table) {
            $table->string('customer_phone_number')->nullable()->change();
            $table->dropColumn(['dipatched_by', 'dispatched_at']);
        });
    }
};
