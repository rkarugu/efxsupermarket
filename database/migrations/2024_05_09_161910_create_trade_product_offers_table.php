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
        Schema::create('trade_product_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trade_agreements_id')->nullable();
            $table->foreign('trade_agreements_id')->references('id')->on('trade_agreements')->onDelete('cascade');
            $table->string('stock_id_code');
            $table->bigInteger('inventory_item_id');
            $table->decimal('offer_amount',20,2);
            $table->timestamps();

            $table->index('inventory_item_id');
            $table->index('stock_id_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_product_offers');
    }
};
