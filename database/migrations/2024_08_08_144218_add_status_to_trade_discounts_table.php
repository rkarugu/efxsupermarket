<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->double('approved_amount')->after('amount');
            $table->boolean('status')->default(0)->after('prepared_by');
        });
    }

    public function down(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->dropColumn('approved_amount');
            $table->dropColumn('status');
        });
    }
};
