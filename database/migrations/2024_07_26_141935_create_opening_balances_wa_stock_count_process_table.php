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
        Schema::create('opening_balances_wa_stock_count_process', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable()->index('ffsdfds748975fskjfkjdsfkjds');
			$table->string('wa_location_and_store_id')->nullable();
			$table->integer('selected_type')->nullable();
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
        Schema::dropIfExists('opening_balances_wa_stock_count_process');
    }
};
