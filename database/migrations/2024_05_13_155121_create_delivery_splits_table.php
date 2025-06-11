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
        Schema::create('delivery_splits', function (Blueprint $table) {
            $table->id();
            $table->double('tonnange_before')->nullable();
            $table->double('tonnange_split')->nullable();
            $table->double('tonnange_remaining')->nullable();
            $table->string('schedule_id')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_splits');
    }
};
