<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->boolean('advance_payment')->after('status')->default(false);
            $table->unsignedBigInteger('mother_lpo')->after('advance_payment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('advance_payment');
            $table->dropColumn('mother_lpo');
        });
    }
};
