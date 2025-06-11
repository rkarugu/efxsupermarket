<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discount_demands', function (Blueprint $table) {
            $table->unsignedInteger('processed_by')->change();
        });
    }

    public function down(): void
    {
        Schema::table('trade_discount_demands', function (Blueprint $table) {
            $table->unsignedInteger('processed_by')->change();
        });
    }
};
