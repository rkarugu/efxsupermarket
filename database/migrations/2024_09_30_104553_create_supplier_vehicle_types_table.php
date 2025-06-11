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
        Schema::create('supplier_vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vehicle_type');
            $table->decimal('tonnage',8,2);
            $table->time('offloading_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_vehicle_types');
    }
};
