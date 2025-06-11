<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discount_demands', function (Blueprint $table) {
            $table->after('amount', function (Blueprint $table) {
                $table->date('start_at');
                $table->date('end_at');
            });
        });
    }

    public function down(): void
    {
        Schema::table('trade_discount_demands', function (Blueprint $table) {
            $table->dropColumn('start_at');
            $table->dropColumn('end_at');
        });
    }
};
