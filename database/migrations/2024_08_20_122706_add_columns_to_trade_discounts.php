<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->after('status', function (Blueprint $table) {
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
            });
        });
    }

    public function down(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }
};
