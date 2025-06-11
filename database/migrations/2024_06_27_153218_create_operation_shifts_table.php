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
        Schema::create('operation_shifts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->boolean('open')->default(true);
            $table->unsignedInteger('restaurant_id')->index();
            $table->boolean('balanced')->default(true);
            $table->boolean('manual_override')->default(false);
            $table->unsignedInteger('authorised_by')->nullable();
            $table->timestamp('authorised_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_shifts');
    }
};
