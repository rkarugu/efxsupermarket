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
        Schema::create('order_location_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('salesman_id');
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->double('salesman_lat')->nullable();
            $table->double('salesman_lng')->nullable();
            $table->double('shop_lat')->nullable();
            $table->double('shop_lng')->nullable();
            $table->double('proximity');
            $table->double('distance');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_location_logs');
    }
};
