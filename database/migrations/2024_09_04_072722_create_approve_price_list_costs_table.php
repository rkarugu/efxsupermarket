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
        Schema::create('approve_price_list_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('trade_agreement_id');
            $table->decimal('price_list_cost', 10, 2);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approve_price_list_costs');
    }
};
