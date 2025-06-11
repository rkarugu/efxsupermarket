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
        Schema::create('opening_balances_wa_stock_check_freezes', function (Blueprint $table) {
            $table->id();
			$table->integer('wa_location_and_store_id')->unsigned()->nullable()->index('wa_location_and_store_id');
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->text('wa_inventory_category_ids')->nullable();
			$table->integer('wa_unit_of_measure_id')->unsigned()->nullable()->index('wa_unit_of_measure_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_balances_wa_stock_check_freezes');
    }
};
