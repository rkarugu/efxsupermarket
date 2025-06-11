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
        Schema::create('geomapping_schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->unsignedInteger('branch');
            $table->unsignedInteger('route_id');
            $table->string('route_manager')->nullable();
            $table->string('route_manager_contact')->nullable();
            $table->string('bizwiz_rep')->nullable();
            $table->string('bizwiz_rep_contact')->nullable();
            $table->string('golden_africa_rep')->nullable();
            $table->string('golden_africa_rep_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geomapping_schedules');
    }
};
