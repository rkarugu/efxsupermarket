<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salesman_shift_store_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('salesman_shifts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('store_id');
            $table->boolean('dispatched')->default(false);
            $table->timestamp('dispatch_time')->nullable();
            $table->unsignedInteger('dispatcher_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salesman_shift_store_dispatches');
    }
};
