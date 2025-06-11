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
        Schema::create('wa_inventory_item_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable()->index('user_id');
            $table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('wa_inventory_item_id');
            $table->integer('store_location_id')->unsigned()->nullable()->index('store_location_id');
            $table->float('selling_price', 10)->nullable()->default(0.00);
            $table->boolean('is_flash')->default(false);
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_inventory_item_prices');
    }
};
