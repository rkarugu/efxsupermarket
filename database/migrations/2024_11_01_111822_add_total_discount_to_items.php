<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wa_purchase_order_items', function (Blueprint $table) {
            $table->double('other_discounts_total');
        });
    }

    public function down(): void
    {
        Schema::table('wa_purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('other_discounts_total');
        });
    }
};
