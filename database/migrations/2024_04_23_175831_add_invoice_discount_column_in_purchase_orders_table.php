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
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->decimal('invoice_discount_per',10,2)->nullable()->default(0);
            $table->decimal('invoice_discount',10,2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('invoice_discount_per');
            $table->dropColumn('invoice_discount');
        });
    }
};
