<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->unsignedInteger('trade_agreement_discount_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->dropColumn('trade_agreement_discount_id');
        });
    }
};
