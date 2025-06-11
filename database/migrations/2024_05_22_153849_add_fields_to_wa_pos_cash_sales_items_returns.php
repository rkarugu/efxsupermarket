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
            $table->unsignedInteger('accepted_by')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->boolean('accepted')->default(false);
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->dropColumn(['accepted_by','accepted_at','comment','accepted']);
        });
    }
};
