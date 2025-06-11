<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_discounts', function (Blueprint $table) {
            $table->id()->startingValue(10000);
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('invoice_id');
            $table->string('supplier_invoice_number');
            $table->date('invoice_date');
            $table->string('description');
            $table->double('amount');
            $table->unsignedBigInteger('prepared_by');
            $table->timestamps();
        });

        Schema::create('trade_discount_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_discount_id');
            $table->unsignedBigInteger('invoice_item_id');
            $table->string('item_code');
            $table->string('discount_type');
            $table->double('discount_value');
            $table->double('item_quantity');
            $table->double('item_cost');
            $table->double('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_discounts');

        Schema::dropIfExists('trade_discount_items');
    }
};
