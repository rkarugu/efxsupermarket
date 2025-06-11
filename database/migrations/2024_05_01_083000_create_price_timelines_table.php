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
        Schema::create('price_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wa_inventory_item_id')->nullable();
            $table->unsignedBigInteger('wa_supplier_id')->nullable();
            $table->string('stock_id_code')->nullable();
            $table->char('transcation_type')->nullable();
            $table->double('current_standard_cost')->nullable();
            $table->double('standart_cost_unit')->nullable();
            $table->string('qoh_before')->nullable();
            $table->string('qty_received')->nullable();
            $table->string('qoh_new')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedInteger('restaurant_id')->nullable();
            $table->string('wa_location_and_store_id')->nullable();
            $table->double('delta')->nullable();
            $table->double('current_selling_price')->nullable();
            $table->double('selling_price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_timelines');
    }
};
