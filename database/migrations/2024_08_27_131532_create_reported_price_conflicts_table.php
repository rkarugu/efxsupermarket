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
        Schema::create('reported_price_conflicts', function (Blueprint $table) {
            $table->id();
            $table->integer('wa_inventory_item_id');
            $table->double('current_selling_price');
            $table->double('current_standard_cost');
            $table->double('reported_price');
            $table->string('image');
            $table->integer('reported_by');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reported_price_conflicts');
    }
};
