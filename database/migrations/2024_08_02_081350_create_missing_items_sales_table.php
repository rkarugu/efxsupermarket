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
        Schema::create('missing_items_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('shift_id');
            $table->integer('salesman_id');
            $table->string('invoice_number');
            $table->integer('wa_inventory_item_id');
            $table->integer('order_quantity');
            $table->integer('qoh');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_items_sales');
    }
};
