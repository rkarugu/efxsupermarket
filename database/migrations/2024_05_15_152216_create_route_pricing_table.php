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
        Schema::create('route_pricing', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger('wa_inventory_item_id');
            $table->unsignedInteger('restaurant_id');
            $table->text('route_id');
            $table->decimal('price');
            $table->boolean('is_flash')->default(0);
            $table->unsignedInteger('created_by');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_pricing');
    }
};
