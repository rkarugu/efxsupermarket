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
        Schema::create('offsite_shift_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->nullable()->constrained('salesman_shifts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('route_id');
            $table->unsignedBigInteger('salesman_id');
            $table->string('reason');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offsite_shift_requests');
    }
};
