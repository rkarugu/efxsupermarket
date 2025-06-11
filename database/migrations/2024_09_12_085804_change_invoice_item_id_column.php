<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trade_discount_items', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_item_id')->nullable()->change();
        });

        Schema::table('trade_discounts', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->change();
            $table->date('invoice_date')->nullable()->change();
            $table->date('invoice_amount')->nullable()->change();
        });
    }

    public function down(): void
    {        
    }
};
