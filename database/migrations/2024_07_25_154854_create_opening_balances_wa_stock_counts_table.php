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
        Schema::create('opening_balances_wa_stock_counts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable()->index('ffsdfds748975fskjfkjdsfkjds');
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('ffsdfds748975fskjfkjdsfkjr5ds4d');
			$table->integer('wa_inventory_item_id')->unsigned()->nullable()->index('ffsdfds748975fskjfkjdsfkjds4d');
			$table->integer('category_id')->unsigned()->nullable()->index('category_id');
			$table->string('item_name')->nullable();
			$table->decimal('quantity', 10)->default(0.00);
			$table->string('uom')->nullable();
			$table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balances_wa_stock_counts');
    }
};
