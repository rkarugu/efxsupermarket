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
        Schema::create('wa_stock__count_variation', function (Blueprint $table) {
			$table->integer('user_id')->unsigned()->nullable();
			$table->integer('wa_location_and_store_id')->unsigned()->nullable();
			$table->integer('wa_inventory_item_id')->unsigned();
			$table->integer('category_id')->unsigned()->nullable();
			$table->decimal('quantity_recorded', 10)->default(0.00);
            $table->decimal('current_qoh', 10)->default(0.00);
            $table->string('variation', 10)->default('0.00')->comment('current_qoh  - quantity_recorded');
			$table->integer('uom_id')->unsigned()->nullable();
			$table->string('reference')->nullable();
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_stock__count_variation');
    }
};
