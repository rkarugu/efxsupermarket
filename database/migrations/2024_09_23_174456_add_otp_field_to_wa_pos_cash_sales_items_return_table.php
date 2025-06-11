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
            $table->unsignedInteger('otp')->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_pos_cash_sales_items_return', function (Blueprint $table) {
            $table->dropColumn(['otp','otp-verified','status']);
        });
    }
};
