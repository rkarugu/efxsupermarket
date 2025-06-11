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
        Schema::create('custom_delivery_shifts', function (Blueprint $table) {
            $table->id();
            $table->timestamp('shift_date');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('fueling_branch_id');
            $table->string('shift_types')->nullable();
            $table->string('document_numbers')->nullable();
            $table->string('shift_status');
            $table->timestamp('shift_start_time')->nullable();
            $table->timestamp('shift_end_time')->nullable();
            $table->double('tonnage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_delivery_shifts');
    }
};
