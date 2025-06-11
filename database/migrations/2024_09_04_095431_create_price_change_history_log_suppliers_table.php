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
        Schema::create('price_change_history_log_suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wa_supplier_id');
            $table->unsignedBigInteger('wa_inventory_item_id');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_change_history_log_suppliers');
    }
};
