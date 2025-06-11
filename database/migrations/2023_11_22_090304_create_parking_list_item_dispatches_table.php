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
        Schema::create('parking_list_item_dispatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('receiving_person_id')->nullable();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('inventory_items_id'); 
            $table->integer('total_quantity')->default(0);
            $table->foreign('shift_id')->on('salesman_shifts')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('store_id')->on('wa_location_and_stores')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('receiving_person_id')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('inventory_items_id')->on('wa_inventory_items')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_list_item_dispatches');
    }
};
