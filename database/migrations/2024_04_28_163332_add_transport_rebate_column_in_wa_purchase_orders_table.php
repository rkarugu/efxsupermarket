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
            $table->decimal('transport_rebate_discount',10,2)->nullable()->default(0);
            $table->decimal('transport_rebate_discount_value',10,2)->nullable()->default(0);
            $table->string('transport_rebate_discount_type',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('transport_rebate_discount');
            $table->dropColumn('transport_rebate_discount_value');
            $table->dropColumn('transport_rebate_discount_type');
        });
    }
};
