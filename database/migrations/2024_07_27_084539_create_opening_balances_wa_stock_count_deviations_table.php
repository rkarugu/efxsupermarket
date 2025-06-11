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
        Schema::create('opening_balances_wa_stock_count_deviations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('batch_date')->nullable();
			$table->integer('wa_inventory_item_id')->unsigned()->nullable();
			$table->integer('wa_location_and_store_id')->unsigned()->nullable();
			$table->string('uom')->nullable();
			$table->decimal('quantity', 10)->nullable();
			$table->decimal('quantity_on_hand', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balances_wa_stock_count_deviations');
    }
};
