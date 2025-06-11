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
        Schema::table('trade_agreement_discounts', function (Blueprint $table) {
            $table->decimal('discount_value',20,2)->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_agreement_discounts', function (Blueprint $table) {
            $table->decimal('discount_value')->nullable()->default(0)->change();
        });
    }
};
