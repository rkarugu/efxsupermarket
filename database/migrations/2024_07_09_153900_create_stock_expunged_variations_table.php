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
        Schema::create('stock_expunged_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable();
			$table->integer('wa_location_and_store_id')->unsigned()->nullable();
			$table->integer('wa_inventory_item_id')->unsigned();
			$table->integer('category_id')->unsigned()->nullable();
			$table->decimal('quantity_recorded', 10)->default(0.00);
            $table->decimal('current_qoh', 10)->default(0.00);
            $table->string('variation', 10)->default('0.00');
			$table->integer('uom_id')->unsigned()->nullable();
			$table->string('reference')->nullable();
            $table->timestamp('created_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_expunged_variations');
    }
};
