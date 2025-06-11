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
        Schema::create('route_auto_breaks', function (Blueprint $table) {
            $table->id();
            $table->string('stb_number')->index();
            $table->unsignedInteger('child_item_id');
            $table->unsignedInteger('child_bin_id')->nullable();
            $table->double('child_quantity');
            $table->string('child_pack_size');
            $table->unsignedInteger('mother_item_id');
            $table->unsignedInteger('mother_bin_id')->nullable();
            $table->double('mother_quantity');
            $table->string('mother_pack_size');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('shift_id');
            $table->unsignedInteger('salesman_id');
            $table->unsignedInteger('invoice_id');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_auto_breaks');
    }
};
